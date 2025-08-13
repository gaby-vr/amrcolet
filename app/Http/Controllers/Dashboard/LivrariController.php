<?php

namespace App\Http\Controllers\Dashboard;

use App\Billing\PaymentGateway;
use App\Courier\CourierGateway;
use App\Exports\ExportOrders;
use App\Models\Invoice;
use App\Models\Livrare;
use App\Models\LivrareCancelRequest;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use App\Traits\OrderInvoiceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Excel;
use PDF;

class LivrariController extends Controller
{
    use OrderInvoiceTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', $this->commonParameters());
    }

    public function view(Request $request, Livrare $livrare)
    {
        return view('profile.dashboard', [
            'item_id' => $livrare->id,
        ] + $this->commonParameters());
    }

    public function commonParameters()
    {
        return [
            'section' => 'orders',
            'subsection' => null,
            'title' => __('Lista comenzi')
        ];
    }

    public function cancel(Livrare $livrare)
    {
        if($livrare->user_id != auth()->id() && $livrare->email != auth()->user()->email)
        {
            return redirect()->route('home');
        }
        $attributes = Validator::make(request()->input(), [
            'type' => ['required', 'integer', 'min:1', 'max:2'],
        ])->validate();

        if($livrare->payed == '0' && $attributes['type'] == 2)
        {
            return back()->with([
                'error' => __('Comanda nu a fost platita, anularea se poate face doar cu ramburs in credite daca au fost folosite')
            ]);
        }

        if(auth()->user()->role == 2) {
            // if it is not already cancelled
            if($livrare->status != '5') {
                $success = false;
                if($livrare->api_shipment_id) {
                    switch ($livrare->api) {
                        case 1:
                            $success = true;
                            // only by phone
                            break;
                        case 2:
                            $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                            $success = $courierGateway->cancelOrder(['shipmentId' => $livrare->api_shipment_id]);
                            break;
                        case 3:
                            $courierGateway = app(CourierGateway::class, ['type' => $livrare->api]);
                            $success = $courierGateway->cancelOrder(['awb' => $livrare->api_shipment_id]);
                            break;
                        default:
                            break;
                    }
                } else {
                    $success = true;
                }

                if($success === true)
                {
                    $user = $livrare->user;
                    $account_balance = $user ? $user->meta('account_balance') : '';
                    if($livrare->invoice != null && $livrare->payed == '1')
                    {
                        if($attributes['type'] == '1') {

                            if($account_balance != '') {
                                $user->setMeta(
                                    'account_balance', 
                                    $account_balance + $livrare->value
                                );
                            } else {
                                $user->setMeta('account_balance', $livrare->value);
                            }
                            $response['status'] = true;

                        } else {
                            $response = self::cancelMobilpay($livrare);
                        }
                        if($response['status']) {

                            $livrare->status = 5;
                            $livrare->save();
                            session()->flash('success', __('Comanda a fost anulata cu succes'));
                            return back();
                        } else {
                            session()->flash('error', $response['error']);
                            return back();
                        }
                    } else {
                        if($livrare->nr_credits_used > 0) {
                            if($account_balance != '') {
                                $user->setMeta(
                                    'account_balance', 
                                    $account_balance + $livrare->nr_credits_used
                                );
                            } else {
                                $user->setMeta('account_balance', $livrare->nr_credits_used);
                            }
                        } else {
                            if($account_balance != '') {
                                $user->setMeta(
                                    'account_balance', 
                                    $account_balance + $livrare->value
                                );
                            } else {
                                $user->setMeta('account_balance', $livrare->value);
                            }
                        }
                        $livrare->status = 5;
                        $livrare->save();
                        session()->flash('success', __('Comanda a fost anulata cu succes'));
                        return back();
                    }
                } elseif(isset($success['error']) && isset($success['error']['message'])) {
                    session()->flash('error', $success['error']['message']);
                }
            }
            session()->flash('error', __('Comanda nu a putut fi anulata'));
        } else {
            $livrare->status = 6;
            LivrareCancelRequest::create([
                'livrare_id' => $livrare->id,
                'type' => $attributes['type'],
            ]);
            $livrare->save();
        }
        return back();
    }

    public function cancelMobilpay(Livrare $livrare)
    {
        $invoice = $livrare->invoice;
        $orderId = $invoice->meta('mobilpay_order_id');
        if($orderId != '')
        {
            $paymentGateway = app(PaymentGateway::class);
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
                        if(strpos($info['name'], 'product_') <= -1) {
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
                } else {
                    return [
                        'status' => false,
                        'error' => __('Factura nu mai are comenzi ce pot fi anulate'),
                    ];
                }
            } else {
                // flash session with the response error
                return [
                    'status' => false,
                    'error' => $response,
                ];
            }
        } else {
            return [
                'status' => false,
                'error' => __('Comanda nu are un id de cumparare mobilpay'),
            ];
        }
    }

    public function awb(Livrare $livrare)
    {   
        if($livrare->api_shipment_id)
        {
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
                    $array['paperSize'] = auth()->user()->meta('print_paper_size');
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

    public function repeat(Request $request, Livrare $livrare)
    {
        $parcels = [];
        if($livrare->type == '1')
        {
            foreach($livrare->packages as $index => $package) {
                $parcels['width'][$index] = $package->width;
                $parcels['length'][$index] = $package->length;
                $parcels['height'][$index] = $package->height;
                $parcels['weight'][$index] = $package->weight;
            }
        }
        $sender = $livrare->sender;
        $receiver = $livrare->receiver;
        session([
            'sender' => $sender ? ($sender->toArray() + [
                'phone_full' => $sender['phone'] ?? null,
                'phone_2_full' => $sender['phone_2'] ?? null,
            ]) : [],
            'receiver' => $receiver ? ($receiver->toArray() + [
                'phone_full' => $receiver['phone'] ?? null,
                'phone_2_full' => $receiver['phone_2'] ?? null,
            ]) : [],
            'package' => [
                'type' => $livrare->type,
                'nr_colete' => $livrare->nr_colete,
                'content' => $livrare->content,
                'awb' => $livrare->awb,
                'delivered_on' => $livrare->delivered_on,
                'pickup_day' => now()->format('Y-m-d'),
                'start_pickup_hour' => $livrare->start_pickup_hour,
                'end_pickup_hour' => $livrare->end_pickup_hour,
                'option' => [
                    'work_saturday' => $livrare->work_saturday,
                    'open_when_received' => $livrare->open_when_received,
                    'retur_document' => $livrare->retur_document,
                ],
                'ramburs' => $livrare->ramburs,
                'ramburs_value' => $livrare->ramburs_value,
                'titular_cont' => $livrare->titular_cont,
                'iban' => $livrare->iban,
                'customer_reference' => $livrare->customer_reference,
                'voucher' => $livrare->voucher_code,
            ] + $parcels
        ]);
        return redirect()->route('order.index')->with(['repeat' => 1]);
    }

    public function showFile(Livrare $livrare, Invoice $invoice, $stream = TRUE)
    {   
        if($invoice->user_id != auth()->id() && $livrare->user_id != auth()->id() && $livrare->email != auth()->user()->email) {
            return redirect()->route('home');
        }
        return view('invoice.invoice', [
            'factura' => $invoice,
        ]);
    }

    public function downloadExcel(Request $request)
    {
        try {
            return Excel::download(new ExportOrders([
                'owned' => true,
                'to' => $request->input('to') 
                    ? Carbon::createFromFormat('d/m/Y', $request->input('to'))->format('Y-m-d') 
                    : null,
                'from' => $request->input('from') 
                    ? Carbon::createFromFormat('d/m/Y', $request->input('from'))->format('Y-m-d') 
                    : null,
            ] + $request->all()), config('app.name').'_comenzi_'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => __('A avut loc o eroare, va rog incercati mai tarziu.')]);
        }
    }
}
