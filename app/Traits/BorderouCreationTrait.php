<?php

namespace App\Traits;

use App\Billing\PaymentGateway;
use App\Courier\CourierGateway;
use App\Exports\ExportBorderouri;
use App\Models\Borderou;
use App\Models\BorderouAwb;
use App\Models\BorderouLivrare;
use App\Models\Repayment;
use App\Models\Livrare;
use App\Models\LivrareStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

trait BorderouCreationTrait
{
    public $cargus_payments = [];

    public function updateBorderou(Borderou $borderou = null, User $user = null, $auto = true)
    {
        if($borderou === null || ($borderou->transformDate('end_date', 'Y-m-d') < now()->format('Y-m-d'))/* && $auto*/) {
            if($borderou !== null) {
                $current_borderou = $borderou;
            }
            $borderou = $this->createBorderou($user ?? ($borderou ? $borderou->user : null) ?? auth()->user(), 0);
        }

        $this->cargusBorderou($borderou);
        $this->dpdBorderou($borderou);

        if($auto) {
            $this->checkBorderouDate($current_borderou ?? $borderou, $user, false/*isset($current_borderou) ? false : true*/);
        }

        Borderou::doesntHave('borderouAwbs')->whereDate('end_date', '<', now()->format('Y-m-d'))->delete();
        return back();
    }

    public function createBorderou(User $user, $add_days = 1)
    {
        $user->withMetas('frequency_', keep:true);
        $lastBorderou = $user->borderouri()->orderByDesc('end_date')->first();
        if($lastBorderou && $lastBorderou->payed_at !== null) {
            $start_date = Carbon::parse($lastBorderou->payed_at)->addDays(1);
        } else {
            $start_date = now()->addDays($add_days);
        }

        if($user->frequency_type == '1') {
            
            $func = 'add'.(ucfirst(strtolower($user->frequency_time ?? 'days')));
            $end_date = $start_date->copy()->{$func}($user->frequency_recurrence);
        } elseif($user->frequency_type == '2') {
            $dates = is_array($user->frequency_dates) 
                ? $user->frequency_dates 
                : json_decode($user->frequency_dates, true);

            if(is_array($dates)) {
                sort($dates);
                for($i = 0 ; $i < count($dates) ; $i++) {
                    $start_date = isset($dates[$i - 1]) && $dates[$i - 1] < $dates[$i]
                        ? now()->setDay($dates[$i - 1])->addDays(1)
                        : now()->subMonths(1)->setDay($dates[0])->addDays(1);

                    $end_date = now()->format('j') <= $dates[$i]
                        ? now()->setDay($dates[$i])
                        : now()->addMonths(1)->setDay($dates[0]);
                }
            }
        }
        return $user->borderouri()->create([
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => isset($end_date) ? $end_date->format('Y-m-d') : $start_date->copy()->addDays(7)->format('Y-m-d'),
        ]);
    }

    public function checkBorderouDate(Borderou $borderou = null, User $user = null, $create = true)
    {
        if($borderou && $borderou->transformDate('end_date', 'Y-m-d') <= now()->format('Y-m-d') && $borderou->payed_at === null) {
            $this->sendApiRequestsBorderou($borderou);
            if($create) {
                return $this->createBorderou($user ?? ($borderou ? $borderou->user : null) ?? auth()->user());
            }
        }
        return $borderou;
    }

    public function sendApiRequestsBorderou(Borderou $borderou)
    {
        $iban_groups = $borderou->borderouAwbs->groupBy('iban');
        $requests = [];
        foreach($iban_groups as $iban => $group) {
            // sum awbs ramburs
            $payment = $group->sum('payment');
            // send libra intent
            $paymentGateway = app(PaymentGateway::class, ['type' => 2]);
            $response = $paymentGateway->setPaymentIntent([
                'amount' => $payment,
                'creditor_name' => $group[0]['account_owner'],
                'creditor_iban' => $iban,
                // 'creditor_iban' => "RO60BREL0002001882890100" ?? $iban,
                'description' => __('Rambursuri incasate pentru client CF borderou #:id', ['id' => $borderou->id]),
            ]);
            $requests[] = [
                'guid' => $response['guid'], 
                'iban' => $iban,
                // 'iban' => "RO60BREL0002001882890100" ?? $iban,
                'account_owner' => $group[0]['account_owner'],
                // 'account_owner' => "Amr toys shop srl" ?? $group[0]['account_owner'],
                'value' => $payment,
                'status' => $response['status'],
                'header' => json_encode($response['header']),
                'body' => json_encode($response['body']),
                'response' => is_array($response['response']) ? json_encode($response['response']) : $response['response'],
                'payment_id' => isset($response['response']['paymentId']) 
                    ? $response['response']['paymentId'] 
                    : null,
            ];
        }
        if(count($requests)) {
            $borderou->borderouApiRequests()->createMany($requests);
        }
    }

    public function checkStatusApiRequestsBorderou(Borderou $borderou)
    {
        $paymentGateway = app(PaymentGateway::class, ['type' => 2]);
        // $status = true;
        $status = false;
        foreach($borderou->borderouApiRequests as $api_request) {
            $response = $paymentGateway->getPaymentStatus([
                'iban' => $api_request->iban,
                'payment_id' => $api_request->payment_id,
            ]);
            // if(auth()->id() == 1) {
            //     $responses[] = $response;
            // }
            if(
                $response['status'] == 200 
                && isset($response['response']['transactionStatus']) 
                && $response['response']['transactionStatus'] == 'PRCS'
            ) {
                $status = true;
            }
            // if(
            //     $response['status'] != 200 
            //     || !isset($response['response']['transactionStatus']) 
            //     || $response['response']['transactionStatus'] != 'PRCS'
            // ) {
            //     $status = false;
            //     \Log::info('Borderou id: '.$borderou->id);
            //     \Log::info($response);
            // } elseif($response['response']['transactionStatus'] == 'PRCS') {
            //     \Log::info('Borderou id: '.$borderou->id);
            //     \Log::info($response);
            // }
        }
        // dd($responses);
        if($status === true && $borderou->payed_at === null) {
            $borderou->update(['payed_at' => now()]);
            $borderou->repayments()->update([
                'status' => 1,
                'type' => 2,
                'payed_on' => now(),
            ]);
            if($borderou->user && $borderou->user->meta('notifications_ramburs_active')) {
                try {
                    $email = $borderou->user->meta('notifications_ramburs_email') ?? $borderou->user->email;
                    Mail::to($email)->send(new \App\Mail\SendBorderouPayedNotification(['borderou' => $borderou]));
                } catch(\Exception $e) { \Log::info($e); }
            }
        }
    }

    public function cargusBorderou(Borderou $borderou)
    {
        $courierGateway = app(CourierGateway::class, ['type' => 1]);
        $end_date = Carbon::parse($borderou->end_date)->addHours(24)->format('Y-m-d');
        for ($i = Carbon::parse($borderou->start_date)->format('Y-m-d') ; $i <= $end_date ; $i = Carbon::parse($i)->addDays(1)->format('Y-m-d')) {
            if(!isset($this->cargus_payments[$i])) {
                $p1 = $courierGateway->getPayment($i);
                $p2 = $courierGateway->getPayment($i, 2);
                $this->cargus_payments[$i] = array_merge(
                    is_array($p1) ? $p1 : [],      // first account
                    is_array($p2) ? $p2 : [],      // second account
                );
            }
            $cargus_payments[$i] = $this->cargus_payments[$i];
        }
        $payments = \Arr::collapse(array_values($cargus_payments));

        if(is_array($payments) && isset($payments[0]['BarCode'])) {
            $user = $borderou->user;
            // get array of awbs which were payed
            $payments = collect($payments)->whereNotNull('RepaymentDate')->pluck('BarCode')->toArray();

            $this->addNewPaymentsToBorderou($borderou, $payments);
            // // get user orders 
            // $livrari_chunk = collect($payments)->map(function($item) { return (string)$item; })->chunk(300)->toArray();
            // foreach ($livrari_chunk as $chunk) {
            //     $livrari[] = $user->livrari()->whereIn('api_shipment_awb', $chunk ?? [])
            //         // get only new orders to add in borderou
            //         ->whereNotIn('api_shipment_awb', $user->borderouAwbs()->select('awb')
            //             ->whereDate(BorderouAwb::getTableName().'.created_at', '>=', now()->subMonths(1))
            //         )->with(['contacts'])->get();
            // }
            // $livrari = collect($livrari ?? [])->flatten();
            // // $livrari = $user->livrari()->with(['contacts'])->whereIn('api_shipment_awb', $payments ?? [])->get();
            // // get only new orders to add in borderou
            // $awbs = $borderou->borderouAwbs->pluck('awb')->toArray() ?? [];
            // $new_orders = $livrari->whereNotIn('api_shipment_awb', $awbs);
            // $repayments = Repayment::whereIn('awb', $awbs)->where('status', '0')->update([
            //     'date_delivered' => date('Y-m-d'),
            //     'payed_on' => date('Y-m-d'),
            //     'status' => 1,
            // ]);
            // $new_payments = [];

            // foreach(array_values($new_orders->all()) as $i => $new_order) {
            //     $sender = $new_order->contacts->where('type', '1')->first();
            //     $receiver = $new_order->contacts->where('type', '2')->first();
            //     $new_payments[$i] = [
            //         'awb' => $new_order->api_shipment_awb,
            //         'sender_name' => $sender->company ?? $sender->name,
            //         'receiver_name' => $receiver->company ?? $receiver->name,
            //         'order_created_at' => $new_order->created_at,
            //         'payment' => $new_order->ramburs_value,
            //         'iban' => $new_order->iban,
            //         'account_owner' => $new_order->titular_cont,
            //     ];
            // }

            // if(count($new_payments)) {
            //     $borderou->borderouAwbs()->createMany($new_payments);
            // }

            // $borderou->total = $borderou->borderouAwbs()->sum('payment');
            // $borderou->save();
        }
    }

    public function dpdBorderou(Borderou $borderou)
    {
        $courierGateway = app(CourierGateway::class, ['type' => 2]);
        $payments = $courierGateway->getPayments($borderou->start_date, Carbon::parse($borderou->end_date)->addHours(24)->format('Y-m-d H:i:s'));

        if(is_array($payments) && isset($payments[0]['docId'])) {
            // get array of awbs which were payed
            $new_payments = collect($payments)->mapWithKeys(function($item, $key) {
                return [$item['date'] => $item['details'] ?? []];
            })->toArray();

            $payments = [];
            foreach ($new_payments as $date => $items) {
                foreach($items as $payment) {
                    $payments[$payment['shipmentId']] = [
                        'date' => $date,
                        'amount' => $payment['amount'],
                    ];
                }
            }
            $this->addNewPaymentsToBorderou($borderou, array_keys($payments));
        }
    }

    ///
    // TO DO: de adaugat functia gls dupa 2 zile de la status livrat (cautat direct in platforma)
    ///

    public function addNewPaymentsToBorderou(Borderou $borderou, $payments)
    {
        // get borderou user
        $user = $borderou->user;
        // get user orders 
        $livrari_chunk = collect($payments)->map(function($item) { return (string)$item; })->chunk(300)->toArray();
        foreach ($livrari_chunk as $chunk) {
            $livrari[] = $user->orders()->whereIn('api_shipment_awb', $chunk ?? [])
                // get only new orders to add in borderou
                ->where('status', 1)
                ->whereNotIn('api_shipment_awb', $user->borderouAwbs()->select('awb')
                    ->whereDate(BorderouAwb::getTableName().'.created_at', '>=', now()->subMonths(2))
                )->with(['contacts'])->get();
        }
        $new_orders = collect($livrari ?? [])->flatten();

        // update repayments status for client
        $repayments = Repayment::whereIn('awb', $new_orders->pluck('api_shipment_awb')->toArray())
            ->where('status', '0')->update([
                'date_delivered' => date('Y-m-d'),
                'payed_on' => date('Y-m-d'),
                'status' => 1,
            ]);
        
        $new_payments = [];

        ///
        // TO DO: verificare livarari care nu sunt in borderouri si livrate din trecut si au status repayment ajuns in amr
        ///

        $currencies = get_cursuri_valutare_array() ?? [];

        // for($i = 0 ; $i < count(array_values($new_orders->all())) ; $i++) {
        foreach(array_values($new_orders->all()) as $i => $new_order) {
            $sender = $new_order->contacts->where('type', '1')->first();
            $receiver = $new_order->contacts->where('type', '2')->first();
            $new_payments[$i] = [
                'awb' => $new_order->api_shipment_awb,
                'sender_name' => $sender->company ?? $sender->name,
                'receiver_name' => $receiver->company ?? $receiver->name,
                'order_created_at' => $new_order->created_at,
                'payment' => round($new_order->ramburs_value * ($currencies[$new_order->ramburs_currency ?? 'RON'] ?? 1), 2),
                'iban' => $new_order->iban,
                'account_owner' => $new_order->titular_cont,
            ];
        }

        if(count($new_payments)) {
            $borderou->borderouAwbs()->createMany($new_payments);
        }

        $borderou->total = $borderou->borderouAwbs()->sum('payment');
        $borderou->save();
    }

    public function downloadExcel(Request $request, Borderou $borderou)
    {
        if(auth()->user()->is_admin != 1 && $borderou->user_id != auth()->id()) {
            return redirect()->back()->withErrors(['error' => __('Borderoul nu va apartine.')]);
        }
        try {
            return \Excel::download(
                new ExportBorderouri($borderou->borderouAwbs()->forExport()->get()->toArray()), 
                config('app.name').'_borderou_'
                    .$borderou->transformDate('start_date', 'd-m-Y').'-'.$borderou->transformDate('end_date', 'd-m-Y').'.xlsx'
            );
        } catch (\Exception $e) {
            \Log::info(__('Eroare export #:id: :message', ['id' => $borderou->id, 'message' => $e->getMessage()]));
            return redirect()->back()->withErrors(['error' => __('A avut loc o eroare, va rog incercati mai tarziu sau contactati un admin.')]);
        }
    }

    public function attachExcel(Request $request, Borderou $borderou)
    {
        try {
            return \Excel::raw(
                new ExportBorderouri($borderou->borderouAwbs()->forExport()->get()->toArray()), 
                \Maatwebsite\Excel\Excel::XLSX
            );
        } catch (\Exception $e) {
            \Log::info(__('Eroare export #:id: :message', ['id' => $borderou->id, 'message' => $e->getMessage()]));
            return redirect()->back()->withErrors(['error' => __('A avut loc o eroare, va rog incercati mai tarziu sau contactati un admin.')]);
        }
    }
}
