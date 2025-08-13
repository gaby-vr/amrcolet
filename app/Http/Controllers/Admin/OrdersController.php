<?php

namespace App\Http\Controllers\Admin;

use App\Billing\PaymentGateway;
use App\Courier\CourierGateway;
use App\Exports\ExportOrders;
use App\Models\LivrareCancelRequest;
use App\Models\Livrare;
use App\Models\UserMeta;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\Package;
use App\Traits\OrderInvoiceTrait;
use App\Traits\OrderStatusCheckTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Excel;

class OrdersController extends Controller
{
    use OrderInvoiceTrait, OrderStatusCheckTrait;
    
    public function index(Request $request)
    {
        $orders = Livrare::with(['sender','receiver','invoice','cancelRequest']);
        if($request->input()) {
            if($request->input('from') != "")
            {
                $orders->whereDate('livrari.created_at', '>=', $request->input('from'));
            }
            if($request->input('to') != "")
            {
                $orders->whereDate('livrari.created_at', '<=', $request->input('to'));
            }
            if($request->has('status') && $request->input('status') != "")
            {
                $orders->where('livrari.status', $request->input('status'));
            }
            if($request->has('user_email') && $request->input('user_email') != "")
            {
                $orders->where(function($query) use($request) {
                    $query->whereHas('user', function($subquery) use($request) {
                        $subquery->where('email', 'like', $request->input('user_email').'%');
                    })->orWhere('email', 'like', $request->input('user_email').'%');
                });
            }
            if($request->has('awb') && $request->input('awb') != "")
            {
                $orders->JoinAwbLables()->where('livrari.api_shipment_awb', 'like', $request->input('awb').'%')
                    ->orWhere('parcel_awb_list', 'like', '%'.$request->input('awb').'%');

            }
            if($request->has('receiver_name') && $request->input('receiver_name') != "")
            {
                $orders->whereHas('receiver', function($query) use($request) {
                    $query->where('name', 'like', $request->input('receiver_name').'%');
                });
            }
        }
        $orders = $orders->orderByDesc('created_at')->paginate(10);
        return view('admin.orders.show', [
            'orders' => $orders->appends(request()->query()),
            'status_list' => Livrare::statusList(),
            'condtitions' => $request->input(),
        ]);
    }

    public function details(Request $request, Livrare $livrare)
    {
        // if(auth()->id() == 1) {
        //     $this->checkDPDOrder($livrare);
        // }
        return view('admin.orders.details', [
            'order' => $livrare,
            'status_list' => Livrare::statusList(),
            'sender' => $livrare->sender,
            'receiver' => $livrare->receiver,
            'packages' => $livrare->packages,
            'invoice' => $livrare->invoice,
        ]);
    }

    public function updateStatus(Request $request, Livrare $livrare)
    {
        $attributes = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Livrare::statusList()))],
            'manual_status' => ['nullable', 'in:1']
        ]);
        $old = $livrare->status;
        $livrare->update(['status' => $attributes['status'], 'manual_status' => $attributes['manual_status'] ?? null]);
        return back()->with('success', __('Actualizare status din ":old" in ":new" reusita.', [
            'old' => Livrare::statusList($old),
            'new' => Livrare::statusList($attributes['status']),
        ]));
    }

    public function awb(Request $request, Livrare $livrare)
    {
        if($livrare->api_shipment_id){
            switch ($livrare->api) {
                case 1:
                    $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                    $courierGateway->printAWB([
                        'barcode' => $livrare->api_shipment_awb, 
                        'format' => auth()->user()->meta('print_paper_size'), 
                        'TotalWeight' => $livrare->total_weight,
                        'createdAt' => $livrare->created_at
                    ]);
                    break;
                case 2:
                    $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                    $array['parcels'] = $courierGateway->getOrderParcels(['shipmentId' => $livrare->api_shipment_id ]);
                    $array['paperSize'] = $livrare->user->meta('print_paper_size');
                    $array['awb'] = $livrare->api_shipment_awb;
                    $courierGateway->printAWB($array);
                    break;
                case 3:
                    $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                    $array['awb'] = $livrare->api_shipment_id;
                    $courierGateway->printAWB($array);
                    break;
                default:
                    break;
            }
        }
        return back();
    }

    public function destroy(Livrare $livrare)
    {
        $livrare->delete();
        return back()->with('success', __('Livrarea #:id a fost sters cu succes.', ['id' => $livrare->id]));
    }

    public function acceptCancelRequest(Request $request, LivrareCancelRequest $cancelRequest)
    {
        $livrare = $cancelRequest->livrare;
        if($livrare->api_shipment_id && $livrare->status != '5')
        {
            $success = false;
            switch ($livrare->api) {
                case 1:
                    $success = true;
                    // se poate doar telefonic
                    // $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                    // $order = $courierGateway->getOrder([ 'orderId' => $livrare->api_shipment_id]);

                    // $pickUpStartDate = now();
                    // $pickUpEndDate = now()->addHours(2);

                    // $success = $courierGateway->cancelOrder(['locationId' => ($order['LocationId'] ?? null), 'PickupStartDate' => $pickUpStartDate->format('Y-m-d').'T'.$pickUpStartDate->format('H:i:s'), 'PickupEndDate' => $pickUpEndDate->format('Y-m-d').'T'.$pickUpEndDate->format('H:i:s')]);
                    break;
                case 2:
                    $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                    $success = $courierGateway->cancelOrder(['shipmentId' => $livrare->api_shipment_id]);

                    break;
                case 3:
                    $courierGateway = app(CourierGateway::class, [
                        'type'     => $livrare->api,
                    ]);
                    $success = $courierGateway->cancelOrder(['awb' => $livrare->api_shipment_id]);
                    break;
                default:
                    break;
            }

            if($success === true)
            {
                $user = $livrare->user;
                $account_balance = $user->meta('account_balance');
                if($livrare->invoice != null && $livrare->payed == '1')
                {
                    if($cancelRequest->type == '1') {

                        if($account_balance != '') {
                            $user->setMeta('account_balance', $account_balance + $livrare->value);
                        } else {
                            $user->setMeta('account_balance', $livrare->value);
                        }
                        $response['status'] = true;

                    } else {
                        $response = self::cancel($livrare);
                    }
                    if($response['status']) {

                        $livrare->status = 5;
                        $livrare->save();
                        $cancelRequest->delete();

                        return redirect()->route('admin.orders.show')->with([
                            'success' => __('Comanda a fost anulata cu succes')
                        ]);

                    } else {
                        return redirect()->route('admin.orders.show')->with([
                            'error' => $response['error']
                        ]);
                    }
                } else {
                    if($livrare->nr_credits_used > 0) {
                        if($account_balance != '') {
                            $user->setMeta('account_balance', $account_balance + $livrare->nr_credits_used);
                        } else {
                            $user->setMeta('account_balance', $livrare->nr_credits_used);
                        }
                    }
                    $livrare->status = 5;
                    $cancelRequest->delete();
                    $livrare->save();

                    return redirect()->route('admin.orders.show')->with([
                        'success' => __('Comanda a fost anulata cu succes')
                    ]);
                }
            } elseif(isset($success['error']) && isset($success['error']['message'])) {
                return redirect()->route('admin.orders.show')->with([
                    'error' => $success['error']['message']
                ]);
            }
            return redirect()->route('admin.orders.show')->with([
                'error' => __('Comanda nu a putut fi anulata')
            ]);
        } else {
            $user = $livrare->user;
            if($user) {
                $account_balance = $user->meta('account_balance');
                if($livrare->invoice != null && $livrare->payed == '1')
                {
                    if($cancelRequest->type == '1') {

                        if($account_balance != '') {
                            $user->setMeta('account_balance', $account_balance + $livrare->value);
                        } else {
                            $user->setMeta('account_balance', $livrare->value);
                        }
                        $response['status'] = true;

                    } else {
                        $response = self::cancel($livrare);
                    }
                    if($response['status']) {

                        $livrare->status = 5;
                        $livrare->save();
                        $cancelRequest->delete();
                        return redirect()->route('admin.orders.show')->with([
                            'success' => __('Comanda a fost anulata cu succes')
                        ]);

                    } else {
                        return redirect()->route('admin.orders.show')->with([
                            'error' => $response['error']
                        ]);
                    }
                } else {
                    if($livrare->nr_credits_used > 0) {
                        if($account_balance != '') {
                            $user->setMeta('account_balance', $account_balance + $livrare->nr_credits_used);
                        } else {
                            $user->setMeta('account_balance', $livrare->nr_credits_used);
                        }
                    }
                    $livrare->status = 5;
                    $cancelRequest->delete();
                    $livrare->save();
                    return redirect()->route('admin.orders.show')->with([
                        'success' => __('Comanda a fost anulata cu succes')
                    ]);
                }
            }
            // Uncomment after more tests
            // $livrare->delete();
        }

        return redirect()->route('admin.orders.show')->with([
            'error' => __('Livrarea nu are un awb valabil si a fost stearsa')
        ]);
    }

    public function refuseCancelRequest(Request $request, LivrareCancelRequest $cancelRequest)
    {
        $livrare = $cancelRequest->livrare;
        $livrare->status = 0;
        $livrare->save();
        $cancelRequest->delete();
        return redirect()->route('admin.orders.show');
    }

    public function import(Request $request)
    {
        $input = $request->validate([
            'curier' => ['required', 'integer', 'in:1,2'],
            'file' => ['required', 'mimes:ods,odt,xls,xlsx,xlt,xltx,xlsm'],
            'exclude_borderou' => ['nullable', 'integer', 'in:1'],
        ]);

        switch (true) {
            case $input['curier'] == 1:
                $instance = new \App\Imports\CargusOrdersImport;
                break;
            case $input['curier'] == 2:
                $instance = new \App\Imports\DPDOrdersImport($input['exclude_borderou'] ?? null);
                break;
        }

        if(isset($instance)) {
            $awbs = Excel::import($instance, $input['file']);
        }

        $back = back()->with([
            'success' => __('Fisierul a fost importat')
        ]);

        if(session()->has('error_awbs')) {
            $back->withErrors([
                'error' => __('Urmatoarele coduri AWB nu au putut fi importate: :awbs', [
                    'awbs' => implode(', ', session()->get('error_awbs'))
                ])
            ]);
        }

        return $back;
    }

    public function cancel(Livrare $livrare)
    {
        $invoice = $livrare->invoice;
        $orderId = $invoice->meta('mobilpay_order_id');
        if($orderId != '')
        {
            $paymentGateway = app(PaymentGateway::class, [
                'sandbox'                => false,    // false,
                'signature'              => env('MOBILPAY_SELLER_SIGNATURE'),
                'mobilpayUsername'       => env('MOBILPAY_USERNAME'),
                'mobilpayPassword'       => env('MOBILPAY_PASSWORD'),
            ]);
            $response = $paymentGateway->cancelOrder($orderId, $invoice->total);
            if($response == true) 
            {
                if($invoice->livrari()->count() > $invoice->livrari()->where('status', '5')->count()) 
                {
                    $newInvoice = $invoice->replicate();
                    $newInvoice->user_id = $invoice->user_id;
                    $newInvoice->series = Setting::firstWhere('name', 'INVOICE_SERIES')->value;
                    $newInvoice->number = Setting::firstWhere('name', 'INVOICE_NR')->value;
                    $newInvoice->payed_on = now();
                    $newInvoice->status = 3;
                    $newInvoice->total = -1 * $livrare->value + $livrare->nr_credits_used;
                    $newInvoice->save();

                    Setting::firstWhere('name', 'INVOICE_NR')->increment('value');

                    // add product info
                    $newInvoice->setMeta('product_nr_products', '1');
                    $newInvoice->setMeta('product_name_0', 'Stornare comanda #'.$livrare->id);
                    $newInvoice->setMeta('product_qty_0', '1');
                    $newInvoice->setMeta('product_price_0', -1 * $livrare->original_value );
                    if($livrare->type == '1')
                    {
                        $packages = Package::where('livrare_id', $livrare->id)->get();
                        $invoiceDescription = '';
                        foreach($packages as $index => $package) {
                            if($index > 0) {
                                $invoiceDescription .= "\r\n";
                            }
                            $invoiceDescription .= 'Colet '.($index + 1).' - '.$package->weight.'kg, '.$package->width.' x '.$package->length.' x '.$package->height;
                        }
                        $newInvoice->setMeta('product_description_0', $invoiceDescription);
                    } else {
                        $newInvoice->setMeta('product_description_0', 'Plic');
                    }

                    if($livrare->voucher_code != null) {
                        $value = $livrare->voucher_type == '1' ? $livrare->voucher_value : round($livrare->original_value * ($livrare->voucher_value/100), 2);
                        $newInvoice->setMeta('product_nr_products', '2');
                        $newInvoice->setMeta('product_name_1', 'Voucher "'.$livrare->voucher_code.'" '.$livrare->voucher_value.($livrare->voucher_type == '1' ? ' RON' : '%') );
                        $newInvoice->setMeta('product_qty_1', '1');
                        $newInvoice->setMeta('product_price_1', -1 * $value);
                        $newInvoice->setMeta('product_description_1', $livrare->voucher_type == '1' ? 'Valoare' : 'Procent');
                    }

                    if($livrare->nr_credits_used > 0) {
                        $newInvoice->setMeta('product_nr_products', '3');
                        $newInvoice->setMeta('product_name_2', 'Credite extrase din cont');
                        $newInvoice->setMeta('product_qty_2', $livrare->nr_credits_used);
                        $newInvoice->setMeta('product_price_2', 1);
                    }

                    // add client info
                    foreach ($invoice->getMetas() as $info) {
                        if(is_array($info) && strpos($info['name'], 'product_') <= -1) {
                            $newInvoice->setMeta($info['name'], $info['value']);
                        }
                    }

                    // add provider info
                    $newInvoice->setMeta('provider_name', Setting::firstWhere('name', 'PROVIDER_NAME')->value);
                    $newInvoice->setMeta('provider_email', Setting::firstWhere('name', 'PROVIDER_EMAIL')->value);
                    $newInvoice->setMeta('provider_phone', Setting::firstWhere('name', 'PROVIDER_PHONE')->value);
                    $newInvoice->setMeta('provider_address', Setting::firstWhere('name', 'PROVIDER_ADDRESS')->value);
                    $newInvoice->setMeta('provider_nr_reg', Setting::firstWhere('name', 'PROVIDER_NR_REG')->value);
                    $newInvoice->setMeta('provider_iban', Setting::firstWhere('name', 'PROVIDER_IBAN')->value);
                    $newInvoice->setMeta('provider_cui', Setting::firstWhere('name', 'PROVIDER_CUI')->value);
                    $newInvoice->setMeta('provider_cap_social', Setting::firstWhere('name', 'PROVIDER_CAP_SOCIAL')->value);

                    $invoice->status = 1;
                    $invoice->save();

                    if($livrare->nr_credits_used > 0) {
                        $user = $invoice->user;
                        $account_balance = $user ? $user->meta('account_balance') : '';
                        if($account_balance != '') {
                            $user->setMeta('account_balance', $account_balance + $livrare->value);
                        } else {
                            $user->setMeta('account_balance', $livrare->value);
                        }
                    }
                    return [
                        'status' => true,
                        'error' => '',
                    ];
                }
                else
                {
                    return [
                        'status' => false,
                        'error' => __('Factura nu mai are comenzi ce pot fi anulate'),
                    ];
                }
            }
            else
            {
                // flash session with the response error
                return [
                    'status' => false,
                    'error' => $response,
                ];
            }
        }
        else
        {
            return [
                'status' => false,
                'error' => __('Comanda nu are un id de cumparare mobilpay'),
            ];
        }
    }

    public function downloadExcel(Request $request)
    {
        try {
            return Excel::download(new ExportOrders($request->all()), config('app.name').'_comenzi_'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage() ?? __('A avut loc o eroare, va rog incercati mai tarziu.')]);
        }
    }
}