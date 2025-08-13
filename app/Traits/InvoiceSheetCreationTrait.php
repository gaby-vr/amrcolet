<?php

namespace App\Traits;

use App\Exports\ExportInvoiceSheet;
use App\Models\Invoice;
use App\Models\InvoiceSheet;
use App\Models\InvoiceSheetAwb;
use App\Models\Livrare;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait InvoiceSheetCreationTrait
{
    public function updateInvoiceSheet(InvoiceSheet $invoiceSheet = null, User $user = null, $auto = true)
    {
        if($user === null) {
            if($invoiceSheet && $invoiceSheet->user) {
                $user = $invoiceSheet->user;
            } elseif(auth()->check()) {
                $user = auth()->user();
            }
            if(!$user) {
                return back()->withErrors(['error' => __('Nu a fost gasit un utilizator specificat')]);
            }
        }

        if($invoiceSheet === null) {
            $invoiceSheet = $user->lastInvoiceSheet ?? $this->createInvoiceSheet($user);
        }

        if($invoiceSheet && ($invoiceSheet->transformDate('end_date', 'Y-m-d H:i:s') < now()->format('Y-m-d') || $invoiceSheet->payed_at !== null) && $auto == true) {
            $invoiceSheet = $this->createInvoiceSheet($user);
        }

        $this->addNewPaymentsToInvoiceSheet($invoiceSheet);

        InvoiceSheet::doesntHave('sheetAwbs')->whereDate('end_date', '<', now()->format('Y-m-d'))->delete();
        return back();
    }

    /* 
    |--------------------------------------------------------------------------
    | Frequency
    |--------------------------------------------------------------------------
    | 1 - monthly (at the start of each month)
    | 2 - every 2 weeks (1st and 15th of each month)
    */
    public function createInvoiceSheet(User $user)
    {
        $user->withMetaKeys(['sheet_frequency']);
        if($user->sheet_frequency == '2') {
            if(now()->format('d') < 15) {
                $start_date = now()->startOfMonth();
                $end_date = now()->setDay(14)->startOfDay();
            } else {
                $start_date = now()->setDay(15)->startOfDay();
                $end_date = now()->endOfMonth();
            }
        } else {
            $start_date = now()->startOfMonth();
            $end_date = now()->endOfMonth();
        }
        if($user->lastInvoiceSheet) {
            $invoiceSheet = $user->lastInvoiceSheet;
            if(
                $invoiceSheet 
                && $invoiceSheet->transformDate('start_date', 'Y-m-d H:i:s') == $start_date->format('Y-m-d')
                && $invoiceSheet->transformDate('end_date', 'Y-m-d H:i:s') != $end_date->format('Y-m-d')
            ) {
                $invoiceSheet->end_date = $end_date->format('Y-m-d');
                $invoiceSheet->save();
                return $invoiceSheet;
            }
        }
        return $user->invoiceSheets()->create([
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
        ]);
    }

    public function addNewPaymentsToInvoiceSheet(InvoiceSheet $invoiceSheet)
    {
        // get sheet user
        $user = $invoiceSheet->user;
        try {
            // get user orders 
            $new_orders = $user->orders()
                ->whereDate('updated_at', '>=', $invoiceSheet->transformDate('start_date', 'Y-m-d H:i:s'))
                ->whereDate('updated_at', '<=', $invoiceSheet->transformDate('end_date', 'Y-m-d H:i:s'))
                ->whereNotIn('status', [0,5])
                ->whereNotIn('api_shipment_awb', $user->sheetAwbs()->select('awb')->whereNotNull('awb'))
                ->with(['contacts'])->get();
        } catch (\Exception $e) {
            \Log::info('InvoiceSheet error line 91 from InvoiceSheetCreationTrait');
            \Log::info($e->getMessage());
            \Log::info($invoiceSheet);
            \Log::info($user);
            return;
        }
        
        $new_payments = [];

        // for($i = 0 ; $i < count(array_values($new_orders->all())) ; $i++) {
        foreach(array_values($new_orders->all()) as $i => $new_order) {
            $sender = $new_order->contacts->where('type', '1')->first();
            $receiver = $new_order->contacts->where('type', '2')->first();
            $new_payments[$i] = [
                'awb' => $new_order->api_shipment_awb,
                'sender_name' => $sender->company ?? $sender->name,
                'receiver_name' => $receiver->company ?? $receiver->name,
                'order_created_at' => $new_order->created_at,
                'payment' => $new_order->amount,
            ];
        }

        if(count($new_payments)) {
            $invoiceSheet->sheetAwbs()->createMany($new_payments);
        }

        $invoiceSheet->total = $invoiceSheet->sheetAwbs()->sum('payment');
        $invoiceSheet->save();
    }

    public function downloadExcel(Request $request, InvoiceSheet $invoiceSheet)
    {
        if(!auth()->check() || (auth()->user()->is_admin != 1 && $invoiceSheet->user_id != auth()->id())) {
            return redirect()->back()->withErrors(['error' => __('Borderoul de facturi nu va apartine.')]);
        }
        try {
            return \Excel::download(
                new ExportInvoiceSheet($invoiceSheet), 
                config('app.name').'_borderou_facturi_'
                    .$invoiceSheet->transformDate('start_date', 'd-m-Y').'-'.$invoiceSheet->transformDate('end_date', 'd-m-Y').'.xlsx'
            );
        } catch (\Exception $e) {
            \Log::info(__('Eroare export #:id: :message', ['id' => $invoiceSheet->id, 'message' => $e->getMessage()]));
            return redirect()->back()->withErrors(['error' => __('A avut loc o eroare, va rog incercati mai tarziu sau contactati un admin.')]);
        }
    }

    public function attachExcel(Request $request, InvoiceSheet $invoiceSheet)
    {
        try {
            return \Excel::raw(new ExportInvoiceSheet($invoiceSheet), \Maatwebsite\Excel\Excel::XLSX);
        } catch (\Exception $e) {
            \Log::info(__('Eroare export #:id: :message', ['id' => $invoiceSheet->id, 'message' => $e->getMessage()]));
            return redirect()->back()->withErrors(['error' => __('A avut loc o eroare, va rog incercati mai tarziu sau contactati un admin.')]);
        }
    }
}
