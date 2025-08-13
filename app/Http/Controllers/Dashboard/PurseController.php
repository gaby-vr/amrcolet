<?php

namespace App\Http\Controllers\Dashboard;

use App\Billing\PaymentGateway;
use App\Models\Invoice;
use App\Models\InvoiceMeta;
use App\Models\Livrare;
use App\Models\Setting;
use App\Models\User;
use App\Traits\OrderInvoiceTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PDF;

class PurseController extends Controller
{
    use OrderInvoiceTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'purse',
            'subsection' => null,
            'title' => __('Plata in avans')
        ]);
    }

    public function payInvoices(Request $request)
    {
        $user = auth()->user();

        if($user->role == '1') {
            return redirect()->route('home');
        }
        if($user->unpayedOrders()->count() < 1) {
            session()->flash('error', __('Nu ai de platit nici o livrare.'));
            return redirect()->route('dashboard.purse.show');
        }

        $clientInfo = $user->invoiceInfo();
        $total = -1 * $user->meta('account_balance');

        // add user address
        $address = 'Str. '.$clientInfo['street'].' '.$clientInfo['street_nr'].
        (isset($clientInfo['bl_code']) ? ', Bl. '.$clientInfo['bl_code'] : '').
        (isset($clientInfo['bl_letter']) ? ', Sc. '.$clientInfo['bl_letter'] : '').
        (isset($clientInfo['intercom']) ? ', Interfon '.$clientInfo['intercom'] : '').
        (isset($clientInfo['floor']) ? ', Etaj '.$clientInfo['floor'] : '').
        (isset($clientInfo['apartment']) ? ', Ap./Nr. '.$clientInfo['apartment'] : '');

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'status' => 0,
            'total' => $total,
        ]);

        $paymentGateway = app(PaymentGateway::class, [
            'returnURL'     => route('dashboard.purse.show'),
            'confirmURL'    => route('dashboard.purse.confirm'),
            'amount'        => $total,
            'firstName'     => $user->meta('invoice_first_name'),
            'lastName'      => $user->meta('invoice_last_name'),
            'email'         => $user->email,
            'address'       => $address,
            'phone'         => $user->meta('invoice_phone'),
            'params'        => array( 'invoice_id' => $invoice->id, 'pay_orders' => $user->unpayedOrders()->pluck('id') ),
            'type'          => $user->meta('invoice_is_company') != '' ? 'company' : 'person', // 'company',
        ]);
        // $data['form'] = $paymentGateway->setForm();

        return redirect()->route('dashboard.purse.show')->with([
            'form_mobilpay' => $paymentGateway->setForm()
        ]);
    }

    public function buy(Request $request)
    {
        $validated = Validator::make($request->input(), [
            'money' => ['required', 'numeric', 'min:1','max:9999.99','regex:/^\d+(\.\d{1,2})?$/'],
        ],[
            'money.regex' => __('Campul suma nu trebuie sa aiba mai mult de 2 decimale.'),
        ]);

        $attributeNames = array(
            'money' => __('suma'),
        );

        $validated->setAttributeNames($attributeNames);

        $attributes = $validated->validate();

        $user = auth()->user();

        $clientInfo = $user->invoiceInfo();
        if(count($clientInfo) < 2)
        {
            return redirect()->route('dashboard.purse.show');
        }

        // add user address
        $address = 'Str. '.$clientInfo['street'].' '.$clientInfo['street_nr'].
        (isset($clientInfo['bl_code']) ? ', Bl. '.$clientInfo['bl_code'] : '').
        (isset($clientInfo['bl_letter']) ? ', Sc. '.$clientInfo['bl_letter'] : '').
        (isset($clientInfo['intercom']) ? ', Interfon '.$clientInfo['intercom'] : '').
        (isset($clientInfo['floor']) ? ', Etaj '.$clientInfo['floor'] : '').
        (isset($clientInfo['apartment']) ? ', Ap./Nr. '.$clientInfo['apartment'] : '');

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'status' => 0,
            'total' => $attributes['money'],
        ]);

        $paymentGateway = app(PaymentGateway::class, [
            'returnURL'     => route('dashboard.purse.show'),
            'confirmURL'    => route('dashboard.purse.confirm'),
            'amount'        => $attributes['money'],
            'firstName'     => $user->meta('invoice_first_name'),
            'lastName'      => $user->meta('invoice_last_name'),
            'email'         => $user->email,
            'address'       => $address,
            'phone'         => $user->meta('invoice_phone'),
            'params'        => array( 'invoice_id' => $invoice->id ),
            'type'          => $user->meta('invoice_is_company') != '' ? 'company' : 'person', // 'company',
        ]);
        // $data['form'] = $paymentGateway->setForm();

        session()->flash('form_mobilpay', $paymentGateway->setForm());

        return redirect()->route('dashboard.purse.show');
    }

    public function confirm(Request $request)
    {
        $paymentGateway = app(PaymentGateway::class/*, ['sandbox' => true]*/);
        $data = $paymentGateway->confirm();
        
        if(isset($data['status']) && $data['status'] == 'confirmed')
        {
            // // ///////////////////////////////////////////////
            // if($invoice = Invoice::find($data['params']['invoice_id'])) {
            //     if($invoice->user_id == '1') {
            //         $send = $this->confirmInvoiceApi(
            //             $data['params']['invoice_id'], isset($data['params']['pay_orders']) ? json_decode($data['params']['pay_orders'], true) : false, $data['orderId'], null, true
            //         );
            //         if($send) {
            //             $this->sendNotification($data['params']['invoice_id']);
            //         }
            //         return true;
            //     }
            // }
            // // ///////////////////////////////////////////////
            // $send = self::createInvoice($data['params']['invoice_id'], $data['params']['pay_orders'] ?? false, $data['orderId']);
            // if($send) {
            //     self::sendNotification($data['params']['invoice_id']);
            // }
            if(isset($data['params']['pay_orders'])) {
                Livrare::whereIn('id', $data['params']['pay_orders'])->update(['payed' => 1]);
            }

            $send = $this->confirmInvoiceApi(
                $data['params']['invoice_id'], isset($data['params']['pay_orders']) ? json_decode($data['params']['pay_orders'], true) : false, $data['orderId'], null, true
            );
            if($send) {
                $this->sendNotification($data['params']['invoice_id']);
            }
        }
        elseif(isset($data['status']) && ($data['status'] == 'canceled' || $data['status'] == 'cancelled'))
        {
            Invoice::where('id', $data['params']['invoice_id'])->update([
                'status' => 2, // Anulata
            ]);
            InvoiceMeta::where('invoice_id', $data['params']['invoice_id'])->delete();
        }
        elseif(isset($data['status']) && $data['status'] == 'credit')
        {
            Invoice::where('id', $data['params']['invoice_id'])->update([
                'status' => 3, // Creditata
            ]);
        }
        elseif(isset($data['status']) && $data['status'] == 'rejected')
        {
            Invoice::where('id', $data['params']['invoice_id'])->update([
                'status' => 4, // Respinsa
            ]);
            InvoiceMeta::where('invoice_id', $data['params']['invoice_id'])->delete();
        }
    }

    public function createInvoice($invoice_id, $pay_orders = false, $mobilpay_order_id = null)
    {
        $invoice = Invoice::firstWhere('id', $invoice_id);
        if($invoice->status != 1) 
        {
            $user = User::firstWhere('id', $invoice->user_id);
            Invoice::where('id', $invoice_id)->update([
                'status' => 1,
                'series' => Setting::firstWhere('name', 'INVOICE_SERIES')->value,
                'number' => Setting::firstWhere('name', 'INVOICE_NR')->value,
                'payed_on' => now(),
            ]);
            Setting::firstWhere('name', 'INVOICE_NR')->increment('value');

            if($mobilpay_order_id) {
                $invoice->setMeta('mobilpay_order_id', $mobilpay_order_id);
            }

            $clientInfo = $user->invoiceInfo();

            // add user address
            $address = 'Str. '.$clientInfo['street'].' '.$clientInfo['street_nr'].
            (isset($clientInfo['bl_code']) ? ', Bl. '.$clientInfo['bl_code'] : '').
            (isset($clientInfo['bl_letter']) ? ', Sc. '.$clientInfo['bl_letter'] : '').
            (isset($clientInfo['intercom']) ? ', Interfon '.$clientInfo['intercom'] : '').
            (isset($clientInfo['floor']) ? ', Etaj '.$clientInfo['floor'] : '').
            (isset($clientInfo['apartment']) ? ', Ap./Nr. '.$clientInfo['apartment'] : '');

            // add user info
            $invoice->setMeta('client_last_name', $clientInfo['last_name']);
            $invoice->setMeta('client_first_name', $clientInfo['first_name']);
            $invoice->setMeta('client_email', $user->email);
            $invoice->setMeta('client_phone', $clientInfo['phone']);
            $invoice->setMeta('client_address', $address);
            $invoice->setMeta('client_postcode', $clientInfo['postcode']);
            $invoice->setMeta('client_country', $clientInfo['country']);
            $invoice->setMeta('client_county', $clientInfo['county']);
            $invoice->setMeta('client_locality', $clientInfo['locality']);
            if(isset($clientInfo['landmark']) && $clientInfo['landmark'] != null)
            {
                $invoice->setMeta('client_landmark', $clientInfo['landmark']);
            }
            if(isset($clientInfo['more_information']) && $clientInfo['more_information'] != null)
            {
                $invoice->setMeta('client_more_information', $clientInfo['more_information']);
            }
            if(isset($clientInfo['is_company']) && $clientInfo['is_company'] != null)
            {
                $invoice->setMeta('client_type', 2);
                $invoice->setMeta('client_nume_firma', $clientInfo['company_name']);
                $invoice->setMeta('client_nr_reg', $clientInfo['nr_reg_com'] ?? $clientInfo['nr_reg'] ?? '');
                $invoice->setMeta('client_cui_nif', $clientInfo['cui_nif']);
                if($clientInfo['company_type'] == 1) {
                    $invoice->setMeta('client_company_type', 1);
                } else {
                    $invoice->setMeta('client_company_type', 2);
                }
            } else {
                $invoice->setMeta('client_type', 1);
            }

            $providerInfo = Setting::select('name','value')->where('name', 'like', 'PROVIDER_%')->get()->mapWithKeys(function ($item) {
                return [explode("PROVIDER_", $item['name'])[1] => $item['value']];
            })->toArray();

            // add provider info
            $invoice->setMeta('provider_name', $providerInfo['NAME']);
            $invoice->setMeta('provider_email', $providerInfo['EMAIL']);
            $invoice->setMeta('provider_phone', $providerInfo['PHONE']);
            $invoice->setMeta('provider_address', $providerInfo['ADDRESS']);
            $invoice->setMeta('provider_nr_reg', $providerInfo['NR_REG']);
            $invoice->setMeta('provider_iban', $providerInfo['IBAN']);
            $invoice->setMeta('provider_cui', $providerInfo['CUI']);
            $invoice->setMeta('provider_cap_social', $providerInfo['CAP_SOCIAL']);

            if($pay_orders)
            {
                // add product info
                $orders = $user->unpayedOrders;
                $nr_products = 0;
                $title = '';
                foreach ($orders as $i => $order)
                {
                    $invoice->setMeta('product_name_'.$nr_products, 'Comanda #'.$order->id);
                    $invoice->setMeta('product_qty_'.$nr_products, '1');
                    $invoice->setMeta('product_price_'.$nr_products, $order->original_value );

                    if($order->type == '1')
                    {
                        $packages = $order->packages;
                        $invoiceDescription = '';
                        foreach($packages as $i => $package) {
                            if($i > 0) {
                                $invoiceDescription .= "\r\n";
                            }
                            $invoiceDescription .= 'Colet '.($i + 1).' - '.$package->weight.'kg, '.$package->width.' x '.$package->length.' x '.$package->height;
                        }
                        $invoice->setMeta('product_description_'.$nr_products, $invoiceDescription);
                    } else {
                        $invoice->setMeta('product_description_'.$nr_products, 'Plic');
                    }

                    if($order->voucher_code != null) {
                        $nr_products++;
                        $value = $order->voucher_type == '1' ? $order->voucher_value : round($order->original_value * ($order->voucher_value/100), 2);
                        $invoice->setMeta('product_name_'.$nr_products, 'Voucher "'.$order->voucher_code.'" '.$order->voucher_value.($order->voucher_type == '1' ? ' RON' : '%') );
                        $invoice->setMeta('product_qty_'.$nr_products, '1');
                        $invoice->setMeta('product_price_'.$nr_products, -1 * $value);
                        $invoice->setMeta('product_description_'.$nr_products, $order->voucher_type == '1' ? 'Valoare' : 'Procent');
                        $addStep++;
                    }

                    if($order->nr_credits_used > 0) {
                        $nr_products++;
                        $invoice->setMeta('product_name_'.$nr_products, 'Credite extrase din cont');
                        $invoice->setMeta('product_qty_'.$nr_products, -1 * $order->nr_credits_used);
                        $invoice->setMeta('product_price_'.$nr_products, 1);
                    }

                    $order->invoice_id = $invoice->id;
                    $order->payed = 1;
                    $order->save();

                    $nr_products++;

                    if($i > 0) {
                        $title .= ', ';
                    }
                    $title .= '#'.$order->id;
                }
                $invoice->setMeta('product_nr_products', $nr_products);
                $invoice->save();

                $user->setMeta('account_balance', '0');
                $user->unsetMeta('expiration_date');

                // if($user->meta('notifications_invoice_active') == 1)
                // {
                //     $details['action'] = 1;
                //     $details['invoice_id'] = $invoice->id;
                //     $details['subject'] = __('Comenzile au fost platite');
                //     $details['title'] = __('Plata comenzilor').' '.$title.' '.('a fost reusita');
                //     $details['body'] = __('Suma platita este de :sum RON.<br> Aveti atasata factura.<br> Veti putea descarca sau vizualiza factura in sectiunea de profil.', ['sum' => $invoice->total]);
                //     Mail::to($user->meta('notifications_invoice_email'))->send(new \App\Mail\SendCreditPurchaseNotification($details));
                // }
            } else {
                // add product info
                $invoice->setMeta('product_nr_products', '1');
                $invoice->setMeta('product_name_0', __('Reincarcare cont cu :sum RON', ['sum' => $invoice->total]));
                $invoice->setMeta('product_qty_0', '1' );
                $invoice->setMeta('product_price_0', $invoice->total );

                if($user->meta('account_balance') != '') {
                    $user->setMeta('account_balance', $user->meta('account_balance') + $invoice->total);
                } else {
                    $user->setMeta('account_balance', $invoice->total);
                } 
            }
            return $invoice;
        }
        return false;
    }

    public function sendNotification($invoice_id, $user_id = null)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $user = $user_id instanceof \App\Models\User ? $user_id : $invoice->user;
        if($invoice && $user->meta('notifications_invoice_active') == 1) {
            $details['action'] = 1;
            $details['invoice_id'] = $invoice->id;
            $details['subject'] = __('Reincarcare cont ').config('app.name');
            $details['title'] = __('Reincarcare reusita');
            $details['body'] = __('Contul tau a fost reincarcat cu :sum RON.<br> Aveti atasata factura.<br> Veti putea vizualiza statusul comenzi in sectiunea de profil impreuna cu o copie a facturi.', ['sum' => $invoice->total]);
            try {
                Mail::to($user->meta('notifications_invoice_email'))->send(new \App\Mail\SendCreditPurchaseNotification($details));
            } catch(\Exception $e) { \Log::info($e); }
        }
    }
}
