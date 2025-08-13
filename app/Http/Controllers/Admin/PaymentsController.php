<?php

namespace App\Http\Controllers\Admin;

use App\Billing\PaymentGateway;
use App\Invoicing\InvoiceGateway;
use App\Exports\ExportInvoices;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Traits\OrderInvoiceTrait;
use App\Traits\OrderValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use PostalCode;
use Excel;

class PaymentsController extends Controller
{
    use OrderInvoiceTrait, OrderValidationTrait;

    public function index(Request $request)
    {
        $invoices = Invoice::where('id', '<>', 0);
        if($request->input()) {
            if($request->input('from') != "")
            {
                $invoices->whereDate('invoices.payed_on', '>=', $request->input('from'));
            }
            if($request->input('to') != "")
            {
                $invoices->whereDate('invoices.payed_on', '<=', $request->input('to'));
            }
            if($request->input('status') != "")
            {
                $invoices->where('invoices.status', '=', $request->input('status'));
            }
            if($request->has('email') && $request->input('email') != "")
            {
                $invoices->whereHas('user', function($query) use($request) {
                    $query->where('email', 'like', $request->input('email').'%');
                });
            }
        }
        $invoices = $invoices->orderByDesc('created_at')->paginate(15);
        $invoices->appends($request->query());
        return view('admin.payments.show', [
            'invoices' => $invoices,
            'condtitions' => $request->input(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.payments.create', [
            'nrProducts' => count(old('price') ?? []),
            'old_user' => User::find(old('user_id')),
            'status_list' => Invoice::statusList(),
        ]);
    }

    public function store(Request $request)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $rules = $this->replaceFullPhoneNumberRule(self::rules());
        $names = self::names();
        $input = Validator::make($request->input(),$rules,[],$names)->validate();

        $nrProducts = count($input['price']);
        $total = 0;
        for($i = 0 ; $i < $nrProducts ; $i++) {
            $total += round($input['price'][$i],2) * round($input['qty'][$i],2);
        }

        $invoice = new Invoice();
        $invoice->user_id = $input['user_id'] ?? 0;
        $invoice->series = Setting::firstWhere('name', 'INVOICE_SERIES')->value;
        $invoice->number = Setting::firstWhere('name', 'INVOICE_NR')->value;
        $invoice->payed_on = $input['payed_on'];
        $invoice->status = $input['status'];
        $invoice->total = $total;
        $invoice->save();

        if(auth()->id() != 1) {
            Setting::firstWhere('name', 'INVOICE_NR')->increment('value');
        }

        // add user address
        $address = 'Str. '.$input['street'].
        (isset($input['street_nr']) ? ' Nr. '.$input['street_nr'] : '').
        (isset($input['bl_code']) ? ', Bl. '.$input['bl_code'] : '').
        (isset($input['bl_letter']) ? ', Sc. '.$input['bl_letter'] : '').
        (isset($input['intercom']) ? ', Interfon '.$input['intercom'] : '').
        (isset($input['floor']) ? ', Etaj '.$input['floor'] : '').
        (isset($input['apartment']) ? ', Ap./Nr. '.$input['apartment'] : '');

        if(isset($input['provider_tva'])) {
            // add tva info
            $invoice->setMeta('provider_tva', $input['provider_tva']);
        }

        // add user info
        $invoice->setMeta('client_last_name', $input['last_name']);
        $invoice->setMeta('client_first_name', $input['first_name']);
        $invoice->setMeta('client_email', isset($input['user_id']) ? User::firstWhere('id', $input['user_id'])->email : $input['email']);
        $invoice->setMeta('client_phone', $input['phone']);
        $invoice->setMeta('client_address', $address);
        $invoice->setMeta('client_postcode', $input['postcode']);
        $invoice->setMeta('client_country', $input['country']);
        $invoice->setMeta('client_county', $input['county']);
        $invoice->setMeta('client_locality', $input['locality']);
        if(isset($input['landmark']) && $input['landmark'] != null) {
            $invoice->setMeta('client_landmark', $input['landmark']);
        }
        if(isset($input['more_information']) && $input['more_information'] != null) {
            $invoice->setMeta('client_more_information', $input['more_information']);
        }
        if(isset($input['is_company']) && $input['is_company'] != null) {
            $invoice->setMeta('client_type', 2);
            $invoice->setMeta('client_nume_firma', $input['company_name']);
            $invoice->setMeta('client_nr_reg', $input['nr_reg_com']);
            $invoice->setMeta('client_cui_nif', $input['cui_nif']);
            if($input['company_type'] == 1) {
                $invoice->setMeta('client_company_type', 1);
            } else {
                $invoice->setMeta('client_company_type', 2);
            }
        } else {
            $invoice->setMeta('client_type', 1);
        }

        // add provider info
        $invoice->setMeta('provider_name', Setting::firstWhere('name', 'PROVIDER_NAME')->value);
        $invoice->setMeta('provider_email', Setting::firstWhere('name', 'PROVIDER_EMAIL')->value);
        $invoice->setMeta('provider_phone', Setting::firstWhere('name', 'PROVIDER_PHONE')->value);
        $invoice->setMeta('provider_address', Setting::firstWhere('name', 'PROVIDER_ADDRESS')->value);
        $invoice->setMeta('provider_nr_reg', Setting::firstWhere('name', 'PROVIDER_NR_REG')->value);
        $invoice->setMeta('provider_iban', Setting::firstWhere('name', 'PROVIDER_IBAN')->value);
        $invoice->setMeta('provider_cui', Setting::firstWhere('name', 'PROVIDER_CUI')->value);
        $invoice->setMeta('provider_cap_social', Setting::firstWhere('name', 'PROVIDER_CAP_SOCIAL')->value);

        // add product info
        $invoice->setMeta('product_nr_products', $nrProducts);
        
        for($i = 0 ; $i < $nrProducts ; $i++) {
            $invoice->setMeta('product_name_'.$i, $input['product'][$i]);
            $invoice->setMeta('product_qty_'.$i, round($input['qty'][$i],2));
            $invoice->setMeta('product_price_'.$i, round($input['price'][$i],2));
            $invoice->setMeta('product_description_'.$i, $input['description'][$i]);
        }
        $invoice->setMeta('created_by_admin', '1');

        return redirect()->route('admin.invoices.show');
    }

    public function edit(Request $request, Invoice $invoice)
    {
        $infos = $invoice->metas()->select('name','value')->get();
        $address = $infos->keyBy('name')['client_address']->value ?? '';
        return view('admin.payments.create', [
            'invoice' => $invoice,
            'nrProducts' => count(old('price') ?? []),
            'old_user' => User::find(old('user_id')),
            'status_list' => Invoice::statusList(),
            'client' => $infos->filter(function ($item) {
                    return count(explode("client_", $item->name)) > 1;
                })->mapWithKeys(function ($item) {
                    return [explode("client_", $item->name)[1] => $item->value];
                })->merge([
                    'street' => get_string_between($address, 'Str. ', ' Nr.'),
                    'street_nr' => get_string_between($address, 'Nr. ', ', '),
                    'bl_code' => get_string_between($address, 'Bl. ', ', '),
                    'bl_letter' => get_string_between($address, 'Sc. ', ', '),
                    'intercom' => get_string_between($address, 'Interfon ', ', '),
                    'floor' => get_string_between($address, 'Etaj ', ', '),
                    'apartment' => get_string_between($address, 'Ap./Nr. ', ', '),
                ])->toArray(),
            'products' => $infos->filter(function ($item) {
                    return count(explode("product_", $item->name)) > 1;
                })->mapWithKeys(function ($item) {
                    return [explode("product_", $item->name)[1] => $item->value];
                })->toArray(),
            'provider' => $infos->filter(function ($item) {
                    return count(explode("provider_", $item->name)) > 1;
                })->mapWithKeys(function ($item) {
                    return [explode("provider_", $item->name)[1] => $item->value];
                })->toArray(),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $rules = $this->replaceFullPhoneNumberRule(self::rules());
        $names = self::names();
        if($invoice->meta('created_by_admin') != '1') {
            unset($rules['user_id']);
        }
        $request = $this->trimPhoneNumberSpaces($request);
        $input = Validator::make($request->input(),$rules,[],$names)->validate();

        $nrProducts = count($input['price']);
        $total = 0;
        for($i = 0 ; $i < $nrProducts ; $i++) {
            $total += round($input['price'][$i],2) * round($input['qty'][$i],2);
        }

        if($invoice->meta('created_by_admin') == '1') {
            $invoice->user_id = $input['user_id'] ?? 0;
        }
        $invoice->payed_on = $input['payed_on'];
        $invoice->status = $input['status'];
        $invoice->total = $total;
        $invoice->save();

        // add user address
        $address = 'Str. '.$input['street'].
        (isset($input['street_nr']) ? ' Nr. '.$input['street_nr'] : '').
        (isset($input['bl_code']) ? ', Bl. '.$input['bl_code'] : '').
        (isset($input['bl_letter']) ? ', Sc. '.$input['bl_letter'] : '').
        (isset($input['intercom']) ? ', Interfon '.$input['intercom'] : '').
        (isset($input['floor']) ? ', Etaj '.$input['floor'] : '').
        (isset($input['apartment']) ? ', Ap./Nr. '.$input['apartment'] : '');

        if(isset($input['provider_tva'])) {
            // add tva info
            $invoice->setMeta('provider_tva', $input['provider_tva']);
        } else {
            $invoice->unsetMeta('provider_tva');
        }

        // add user info
        $invoice->setMeta('client_last_name', $input['last_name']);
        $invoice->setMeta('client_first_name', $input['first_name']);
        $invoice->setMeta('client_email', isset($input['user_id']) ? User::firstWhere('id', $input['user_id'])->email : $input['email']);
        $invoice->setMeta('client_phone', $input['phone']);
        $invoice->setMeta('client_address', $address);
        $invoice->setMeta('client_postcode', $input['postcode']);
        $invoice->setMeta('client_country', $input['country']);
        $invoice->setMeta('client_county', $input['county']);
        $invoice->setMeta('client_locality', $input['locality']);
        if(isset($input['landmark']) && $input['landmark'] != null) {
            $invoice->setMeta('client_landmark', $input['landmark']);
        }
        if(isset($input['more_information']) && $input['more_information'] != null) {
            $invoice->setMeta('client_more_information', $input['more_information']);
        }
        if(isset($input['is_company']) && $input['is_company'] != null) {
            $invoice->setMeta('client_type', 2);
            $invoice->setMeta('client_nume_firma', $input['company_name']);
            $invoice->setMeta('client_nr_reg', $input['nr_reg_com']);
            $invoice->setMeta('client_cui_nif', $input['cui_nif']);
            if($input['company_type'] == 1) {
                $invoice->setMeta('client_company_type', 1);
            } else {
                $invoice->setMeta('client_company_type', 2);
            }
        } else {
            $invoice->setMeta('client_type', 1);
            $invoice->unsetMeta('client_nume_firma');
            $invoice->unsetMeta('client_nr_reg');
            $invoice->unsetMeta('client_cui_nif');
            $invoice->unsetMeta('client_company_type');
        }

        $invoice->metas()->where('name', 'like', 'product_%')->delete();

        // add product info
        $invoice->setMeta('product_nr_products', $nrProducts);
        
        for($i = 0 ; $i < $nrProducts ; $i++) {
            $invoice->setMeta('product_name_'.$i, $input['product'][$i]);
            $invoice->setMeta('product_qty_'.$i, round($input['qty'][$i],2));
            $invoice->setMeta('product_price_'.$i, round($input['price'][$i],2));
            $invoice->setMeta('product_description_'.$i, $input['description'][$i]);
        }

        return redirect()->route('admin.invoices.show');
    }

    public function storn(Request $request, Invoice $invoice)
    {
        $orderId = $invoice->meta('mobilpay_order_id');
        if($orderId != '') {
            $paymentGateway = app(PaymentGateway::class, [
                'signature'              => env('MOBILPAY_SELLER_SIGNATURE'),
                'mobilpayUsername'       => env('MOBILPAY_USERNAME'),
                'mobilpayPassword'       => env('MOBILPAY_PASSWORD'),
            ]);
            $response = $paymentGateway->cancelOrder($orderId, $invoice->total);
        } else {
            $response = true;
        }

        if($response == true) {
            if($invoice->credited_by == null) {
                $newInvoice = $invoice->replicate();
                $newInvoice->user_id = $invoice->user_id;
                $newInvoice->series = Setting::firstWhere('name', 'INVOICE_SERIES')->value;
                $newInvoice->number = Setting::firstWhere('name', 'INVOICE_NR')->value;
                $newInvoice->payed_on = now();
                $newInvoice->status = 3;
                $newInvoice->total = -1 * $invoice->total;
                $newInvoice->save();

                Setting::firstWhere('name', 'INVOICE_NR')->increment('value');

                if($invoice->external_link) {
                    try {
                        $api = app(InvoiceGateway::class);
                        $response = $api->stornInvoice(['number' => $invoice->number]);
                    } catch(\Exception $e) { \Log::info($e); }

                    if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {
                        $newInvoice->external_link = $response['data']['link'] ?? null;
                        $newInvoice->series = $response['data']['seriesName'] ?? $newInvoice->series;
                        $newInvoice->number = $response['data']['number'] ? $response['data']['number'] + 0 : $newInvoice->number;
                        $newInvoice->save();
                    }
                } else {

                    // add product info
                    foreach ($invoice->infos as $info) {
                        if(strpos($info->name, 'product_name') > -1) {
                            $newInvoice->setMeta($info->name, 'Stornare '.strtolower($info->value));
                        } elseif(strpos($info->name, 'product_price') > -1) {
                            $newInvoice->setMeta($info->name, -1 * $info->value);
                        } else {
                            $newInvoice->setMeta($info->name, $info->value);
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

                }
                $invoice->credited_by = $newInvoice->id;
                $invoice->save();

                $livrare = $invoice->livrare;
                if($livrare != null && $livrare->nr_credits_used > 0) {
                    $user = $invoice->user;
                    if($user->meta('account_balance') != '') {
                        $user->setMeta('account_balance', $user->meta('account_balance') + $livrare->nr_credits_used);
                    } else {
                        $user->setMeta('account_balance', $livrare->nr_credits_used);
                    }
                }

                return redirect()->route('admin.invoices.show')->with([
                    'status' => __('Factura a fost stornata.')
                ]);
            } else {
                return redirect()->route('admin.invoices.show')->withErrors([
                    'error' => __('Factura este deja stornata.')
                ]);
            }
        } else {
            // flash session with the response error
            return redirect()->route('admin.invoices.show')->withErrors([
                'error' => __('Eroare la stornarea din procesatorul de plata. ID-ul platii este invalid. Contactati un developer.')
            ]);
        }
    }

    public function getInvoice(User $user = null)
    {
        return $user ? json_encode($user->invoiceInfo()) : json_encode([]);
    }

    public function rules()
    {
        return [
            'status' => ['required', 'integer', 'in:1,2,3'],
            'user_id' => ['nullable', 'integer', 'min:1', 'exists:users,id'],
            'email' => ['nullable', 'required_without:user_id', 'required_if:user_id,null', 'email', 'min:1', 'max:255'],
            'payed_on' => ['required', 'date'],
            'provider_tva' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // 'country' => ['required', 'string', 'max:255'],
            // 'country_code' => ['required', 'string', 'min:1', 'max:5'],
            // 'first_name' => ['required', 'string', 'max:255'],
            // 'last_name' => ['required', 'string', 'max:255'],
            // 'phone' => ['required', 'string', 'min:1', 'max:20'],
            // 'postcode' => ['required', 'string', 'min:3', 'max:15', /*PostalCode::for(strtoupper(request()->input('country_code') ?? 'ro'))*/
            //     function ($attribute, $value, $fail) {
            //         if (request()->has('country_code') && request()->input('country_code') == 'ro') {
            //             $oldval = $value;
            //             $value = strval($value)[0] == 0 ? substr($value, -5) : $value;
            //             if(DB::table('coduri_postale')->whereIn('cod_postal', [$value, $oldval])->count() < 1) {
            //                 $fail('Campul cod postal este gresit.');
            //             }
            //         }
            //     },
            // ],
            // 'is_company' => ['nullable', 'integer', 'min:1', 'max:2'],
            // 'company_type' => ['nullable', 'required_if:is_company,1', 'integer', 'min:1', 'max:2'],
            // 'cui_nif' => ['nullable', 'required_if:is_company,1', 'string', 'max:255'],
            // 'nr_reg_com' => ['nullable', 'required_if:is_company,1', 'string', 'max:255'],
            // 'company_name' => ['nullable', 'required_if:is_company,1', 'string', 'max:255'],
            // 'county' => ['required', 'string', 'max:255'],
            // 'locality' => ['required', 'string', 'max:255'],
            // 'street' => ['required', 'string', 'max:255'],
            // 'street_nr' => ['required', 'string', 'max:255'],
            // 'apartment' => ['nullable', 'string', 'max:255'],
            // 'bl_code' => ['nullable', 'string', 'max:255'],
            // 'bl_letter' => ['nullable', 'string', 'max:255'],
            // 'intercom' => ['nullable', 'string', 'max:255'],
            // 'floor' => ['nullable', 'string', 'max:255'],
            // 'landmark' => ['nullable', 'string', 'min:1', 'max:255'],
            // 'more_information' => ['nullable', 'string', 'min:1', 'max:255'],

            'product' => ['required', 'array'],
            'product.*' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'numeric', 'min:0.01', 'max:255'],
            'price' => ['required', 'array'],
            'price.*' => ['required', 'numeric'],
            'description' => ['required', 'array'],
            'description.*' => ['max:255'],
        ] + $this->addressRules('invoice', false);
    }

    public function names()
    {
        return [
            'user_id' => __('utilizator'),
            'email' => __('email'),

            // 'country' => __('tara'),
            // 'name' => __('nume'),
            // 'phone' => __('telefon'),
            // 'company' => __('companie'),
            // 'is_company' => __('persoana'),
            // 'cui_nif' => __('cui/nif'),
            // 'nr_reg_com' => __('nr. reg. com.'),
            // 'company_name' => __('nume companie'),
            // 'postcode' => __('cod postal'),
            // 'county' => __('judet'),
            // 'locality' => __('localitate'),
            // 'street' => __('strada'),
            // 'street_nr' => __('nr. strada'),
            // 'apartment' => __('apartament/nr. casa'),
            // 'bl_code' => __('bloc'),
            // 'bl_letter' => __('scara'),
            // 'intercom' => __('interfon'),
            // 'floor' => __('etaj'),
            // 'landmark' => __('reper'),

            'product' => __('produse'),
            'product.*' => __('produs'),
            'qty' => __('cantitati'),
            'qty.*' => __('cantitate'),
            'price' => __('preturi'),
            'price.*' => __('pret'),
            'description' => __('descrieri'),
            'description.*' => __('descriere')
        ] + $this->addressNames();
    }

    // public function get_string_between($string, $start, $end)
    // {
    //     return explode($end, explode($start, $string)[1] ?? '')[0] ?? '';
    // }

    public function downloadExcel(Request $request)
    {
        return Excel::download(new ExportInvoices($request->input()), config('app.name').'_facturi_'.date('Y-m-d').'.xlsx');
    }
}