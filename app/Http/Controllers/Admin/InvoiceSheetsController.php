<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportInvoiceSheet;
use App\Models\InvoiceSheet;
use App\Models\InvoiceSheetAwb;
use App\Models\Livrare;
use App\Models\Setting;
use App\Models\User;
use App\Traits\InvoiceSheetCreationTrait;
use App\Traits\OrderInvoiceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Validator;

class InvoiceSheetsController extends Controller
{
    use InvoiceSheetCreationTrait, OrderInvoiceTrait;

    public function index(Request $request)
    {
        return view('admin.invoice-sheets.show', [
            'items' => $this->search($request),
            'conditions' => $request->input(),
        ]);
    }

    public function search(Request $request)
    {
        $table = InvoiceSheet::getTableName();
        $items = InvoiceSheet::with(['user','invoice']);
        if($request->input()) {
            if($request->input('from') != "") {
                $items->whereDate($table.'.start_date', '>=', $request->input('from'));
            }
            if($request->input('to') != "") {
                $items->whereDate($table.'.end_date', '<=', $request->input('to'));
            }
            if($request->input('status') != "") {
                $func = $request->input('status') == '1' ? 'whereNotNull' : 'whereNull';
                $items->{$func}($table.'.payed_at');
            }
            if($request->has('user_name') && $request->input('user_name') != "") {
                $items->where(function($query) use($request) {
                    $query->whereHas('user', function($subquery) use($request) {
                        $subquery->where('name', 'like', $request->input('user_name').'%');
                    });
                });
            }
            if($request->has('user_email') && $request->input('user_email') != "") {
                $items->where(function($query) use($request) {
                    $query->whereHas('user', function($subquery) use($request) {
                        $subquery->where('email', 'like', $request->input('user_email').'%');
                    });
                });
            }
        }
        return $items->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());
    }

    public function get(Request $request, User $user = null)
    {
        $search = request()->input('search');
        return json_encode($user 
            ? $user->orders()->select('api_shipment_awb as text')->where('api_shipment_awb', 'LIKE', $search.'%')
                ->whereNotIn('api_shipment_awb', $user->sheetAwbs()->select('awb')
                    ->whereDate(InvoiceSheetAwb::getTableName().'.created_at', '>=', now()->subMonths(2))
                )->orderBy('api_shipment_awb')->take(5)->get()
            : []
        );
    }

    public function create()
    {
        return view('admin.invoice-sheets.create', [
            'user' => User::find(old('user_id')),
            'livrari' => old('livrari'),
        ]);
    }

    public function edit(InvoiceSheet $invoiceSheet)
    {
        $user = User::find(old('user_id', $invoiceSheet->user_id));
        return view('admin.invoice-sheets.create', [
            'item' => $invoiceSheet,
            'user' => $user,
            'livrari' => old('livrari', !empty($invoiceSheet->sheetAwbs->toArray()) 
                ? $invoiceSheet->sheetAwbs->load('order')->each->append('status_text','status_color')->toArray() 
                : ['']),
            'livrari_valabile' => $user->orders()->with(['sender','receiver'])
                ->whereDate('updated_at', '>=', $invoiceSheet->transformDate('start_date', 'Y-m-d'))
                ->whereDate('updated_at', '<=', $invoiceSheet->transformDate('end_date', 'Y-m-d'))
                ->whereNotIn('status', [0,5])
                ->whereNotIn('api_shipment_awb', $user->sheetAwbs()->select('awb'))
                ->take(25)->get(),
        ]);
    }

    public function save(Request $request, InvoiceSheet $invoiceSheet = null)
    {
        $input = $request->merge([
            'livrari' => flip_array_keys($request->input('livrari') ?? ['livrari' => null])
        ])->validate($this->rules($invoiceSheet->id ?? null), [], $this->names());

        $new = false;
        if($invoiceSheet === null) {
            $new = true;
            $invoiceSheet = new InvoiceSheet;
        }

        $input['total'] = collect($input['livrari'] ?? [])->sum('payment') ?? 0;
        if($invoiceSheet && $invoiceSheet->payed_at === null && $input['payed_at'] !== null) {
            $payed = true;
        }
        $invoiceSheet->fill($input)->save();
        $invoiceSheet->sheetAwbs()->delete();
        if(isset($input['livrari'])) {
            $invoiceSheet->sheetAwbs()->createMany($input['livrari']);
        }
        // if(isset($payed)) {
        //     $invoiceSheet->repayments()->update([
        //         'status' => 1,
        //         'type' => 2,
        //         'payed_on' => $input['payed_at'],
        //     ]);
        //     if($invoiceSheet->user && $invoiceSheet->user->meta('notifications_ramburs_active')) {
        //         try {
        //             $email = $invoiceSheet->user->meta('notifications_ramburs_email') ?? $invoiceSheet->user->email;
        //             Mail::to($email)->send(new \App\Mail\SendInvoiceSheetPayedNotification(['InvoiceSheet' => $invoiceSheet]));
        //         } catch(\Exception $e) { \Log::info($e); }
        //     }
        // }

        return redirect()->route('admin.invoice-sheets.edit', $invoiceSheet)->with([
            'success' => $new 
                ? __('Fisa de facturi a fost creata cu succes.')
                : __('Fisa de facturi a fost modificata cu succes.'), 
        ]);
    }

    public function updateInvoiceSheetManual(InvoiceSheet $invoiceSheet)
    {
        return $this->updateInvoiceSheet($invoiceSheet, null, false);
    }

    public function createInvoiceFromSheet(InvoiceSheet $invoiceSheet)
    {
        if($invoiceSheet->invoice) {
            return back()->with([
                'success' => __('Fisa are deja o factura asociata.') 
            ]);
        }
        $invoice = $this->createInvoice($invoiceSheet->total, $invoiceSheet->user, 1);
        $this->addInvoiceClient($invoice, [], true);
        $this->addInvoiceProvider($invoice, [], true);
        $this->addInvoiceProduct($invoice, 0, [
            'name' => 'Servicii intermediere curierat conform anexa',
            'price' => $invoiceSheet->total,
            'nr_products' => 1
        ], true);
        $invoice->setMeta('created_by_admin', '1');

        $invoiceSheet->invoice_id = $invoice->id;
        $invoiceSheet->payed_at = now()->format('Y-m-d');
        $invoiceSheet->save();

        try {
            $this->sendInvoiceToApi($invoice, 0);
        } catch (Exception $e) {
            return back()->withErrors(['error' => __('Eroare la trimiterea in platforma de facturare, contactati un developer.')]);
        }

        try {
            $invoiceSheet->load('invoice');
            if($invoiceSheet->user && $invoiceSheet->user->meta('notifications_invoice_active') && $invoiceSheet->invoice) {
                try {
                    $email = $invoiceSheet->user->meta('notifications_invoice_email') ?? $invoiceSheet->user->email;
                    \Log::info($email);
                    Mail::to($email)->send(new \App\Mail\SendInvoiceSheetPayedNotification(['invoice_sheet' => $invoiceSheet]));
                } catch(\Exception $e) { \Log::info($e); }
            }
        } catch (Exception $e) {
            return back()->withErrors(['error' => __('Mailul nu a putut fi trimis, contactati un developer.')]);
        }

        return back()->with([
            'success' => __('Factura a fost creata pentru acesta fisa.') 
        ]);
    }

    public function export(Request $request, InvoiceSheet $invoiceSheet)
    {
        try {
            return $this->downloadExcel($request, $invoiceSheet);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage() ?? __('A avut loc o eroare, va rog incercati mai tarziu.')]);
        }
    }

    public function destroy(Request $request, InvoiceSheet $invoiceSheet)
    {
        $invoiceSheet->delete();
        return back()->with([
            'success' => __('Fisa de facturi a fost stearsa cu succes.') 
        ]);
    }

    public function rules($id = null)
    {
        $rulesAwbs = InvoiceSheetAwb::rules(false, $id, 'livrari.*.');
        foreach($rulesAwbs as $column => $rules) {
            foreach($rules as $index => $rule) {
                if(in_array($rule, ['nullable','required'])) {
                    $rulesAwbs[$column][$index] = 'sometimes';
                }
            }
        }
        return InvoiceSheet::rules() + $rulesAwbs/* + [
            'livrari.*' => ['sometimes', 'required_array_keys:'.implode(',', array_keys($rulesAwbs))]
        ]*/;
    }

    public function names()
    {
        return InvoiceSheet::names() + InvoiceSheetAwb::names() + \Arr::prependKeysWith(InvoiceSheetAwb::names(), 'livrari.*.') + [
            'livrari.*' => __('Livrare')
        ];
    }
}