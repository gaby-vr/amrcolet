<?php

namespace App\Traits;

use App\Models\CodPostal;
use App\Models\Country;
use App\Models\Curier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PostalCode;

trait OrderValidationTrait
{
    public function guestEmailRules()
    {
        return [
            'to_send_email' => ['required', 'email', 'max:60'],
        ];
    }

    public function addressRules($type = null, $include_type = true)
    {
        $all = $type == '*' ? true : false;
        $prefix = $include_type && in_array($type, ['sender','receiver','invoice']) ? $type.'.' : '';
        return [
            $prefix.'country' => ['required', 'string', 'max:60'],
            $prefix.'country_code' => ['required', 'string', 'min:2', 'max:5'],
            $prefix.'phone' => ['required', 'digits_between:9,20', 'max:20'], // 'digits_between:9,20'
            $prefix.'phone_full' => ['required', 'string', 'regex:/\+('.implode('|', $this->phonePrefixes()).')\d{9,13}?/', 'min:12', 'max:20'],
            $prefix.'postcode' => ['required', 'string', 'min:3', 'max:15', 
                PostalCode::for(strtoupper(request()->input($prefix.'country_code') ?? 'ro')), 
                function ($attribute, $value, $fail) use($prefix) {
                    if (
                        request()->has($prefix.'country_code') 
                        && request()->input($prefix.'country_code') == 'ro'
                    ) {
                        if(CodPostal::where('cod_postal', $value)->count() < 1) {
                            $fail('Campul cod postal este gresit.');
                        }
                    }
                },
            ],
            $prefix.'county' => ['required', 'string', 'max:60'],
            $prefix.'locality' => ['required', 'string', 'max:60'],
            $prefix.'street' => ['required', 'string', 'max:50'],
            $prefix.'street_nr' => ['required', 'string', 'max:10'],
            $prefix.'apartment' => ['nullable', 'string', 'max:10'],
            $prefix.'bl_code' => ['nullable', 'string', 'max:10'],
            $prefix.'bl_letter' => ['nullable', 'string', 'max:10'],
            $prefix.'intercom' => ['nullable', 'string', 'max:10'],
            $prefix.'floor' => ['nullable', 'string', 'max:10'],
            $prefix.'landmark' => ['nullable', 'string', 'max:60'],
            $prefix.'more_information' => ['nullable', 'string', 'max:60'],
        ] + ($all || $type == '' || $type == 'invoice' ? [
            $prefix.'first_name' => ['required', 'string', 'min:3', 'max:60', 'regex:/^(?![0-9]*$)[a-zA-Z0-9]+$/'],
            $prefix.'last_name' => ['required', 'string', 'min:3', 'max:60', 'regex:/^(?![0-9]*$)[a-zA-Z0-9]+$/'],
            $prefix.'is_company' => ['nullable', 'integer', 'min:1', 'max:1'],
            $prefix.'company_type' => ['nullable', 'required_if:'.$prefix.'is_company,1', 'exclude_unless:is_company,1', 'integer', 'min:1', 'max:2'],
            $prefix.'cui_nif' => ['nullable', 'required_if:'.$prefix.'is_company,1', 'exclude_unless:is_company,1', 'string', 'max:60'],
            $prefix.'nr_reg_com' => ['nullable', 'required_if:'.$prefix.'is_company,1', 'exclude_unless:is_company,1', 'string', 'max:60'],
            $prefix.'company_name' => ['nullable', 'required_if:'.$prefix.'is_company,1', 'exclude_unless:is_company,1', 'string', 'min:3', 'max:60'],
        ] : []) + ($all || $type == 'sender' || $type == 'receiver' ? [
            $prefix.'name' => ['required', 'string', 'min:3', 'max:60'], // regex:/\A\p{L}+(?:[-\s]\p{L}+)*\z/ does not allow special char as "ș"
            $prefix.'phone_2' => ['nullable', 'digits_between:9,20', 'max:20'],
            $prefix.'phone_2_full' => ['nullable', 'string', 'regex:/\+('.implode('|', $this->phonePrefixes()).')\d{9,13}?/', 'min:12', 'max:20'],
            $prefix.'email' => [$type == 'receiver' && request()->input($prefix.'country_code') == 'ro' ? 'nullable' : 'required', 'string', 'email'/*:strict,dns*/, 'not_regex:/[ÄäÜüÖö]/', 'min:1', 'max:60'],
            $prefix.'company' => ['nullable', 'string', 'min:3', 'max:60'],
        ] : []);
    }

    public function packageRules()
    {
        return [
            'type' => ['required', 'min:1', 'max:2', 'integer'],
            'nr_colete' => ['nullable', 'exclude_if:type,2', 'required_if:type,1', 'integer', 'min:1'],
            'content' => ['required', 'string', 'max:50'],
            'weight' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'array', 'min:'.(request()->input('nr_colete') ?? 1)],
            'weight.*' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'integer', 'min:1', 'max:200'],
            'length' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'array', 'min:'.(request()->input('nr_colete') ?? 1)],
            'length.*' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'integer', 'min:1', 'max:200'],
            'width' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'array', 'min:'.(request()->input('nr_colete') ?? 1)],
            'width.*' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'integer', 'min:1', 'max:200'],
            'height' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'array', 'min:'.(request()->input('nr_colete') ?? 1)],
            'height.*' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'integer', 'min:1', 'max:200'],
            'volume.*' => ['nullable', 'required_if:type,1', 'exclude_unless:type,1', 'numeric', 'min:0.001', 'max:200'],
            'awb' => ['required', 'min:1', 'max:2', 'integer'],
            'pickup_day' => ['required', 'date', 'after_or_equal:'.date('Y-m-d'), 'before_or_equal:'.date('Y-m-d', strtotime(date('Y-m-d',strtotime('-1 days')).' +3 Weekday'))],
            'start_pickup_hour' => ['required', 'integer', 'min:8', 'max:16'],
            'end_pickup_hour' => ['required', 'integer', 'max:18', 
                'min:'.(request()->has('start_pickup_hour') 
                    ? (int)request()->has('start_pickup_hour') + 2
                    : '10'),

                function ($attribute, $value, $fail) {
                    if (
                        request()->has('pickup_day') 
                        && request()->input('pickup_day') == now()->format('Y-m-d')
                        && $value < now()->addHours(2)->format('H') && $value < '16'
                    ) {
                        $fail(__('Campul :attribute trebuie sa fie cu minim 2 ore in viitor de la ora curenta daca doresti ca aceasta sa fie efectuata astazi.'));
                    }
                },
            ],
            'options.*' => ['nullable', 'max:1'],
            'swap_details.nr_parcels' => ['nullable', 'required_if:options.retur_document,1', 'exclude_unless:options.retur_document,1', 'min:1', 'max:10'],
            'swap_details.total_weight' => ['nullable', 'required_if:options.retur_document,1', 'exclude_unless:options.retur_document,1', 'min:1', 'max:30'],
            // 'send_sms' => ['nullable', 'max:1'],
            'ramburs' => ['required', 'integer', 'in:1,3'],
            'ramburs_value' => ['nullable', 'required_unless:ramburs,1', 'exclude_if:ramburs,1', 'numeric', 'min:1', 'max:1000000'],
            'titular_cont' => ['nullable', 'required_if:ramburs,3', 'string', 'min:1', 'max:32'],
            'iban' => ['nullable', 'required_if:ramburs,3', 'string', 'min:24', 'max:24',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/RO[0-9]{2}('.implode('|', $this->bankCodes()).')[A-Z0-9]{16}/', $value)) {
                        $fail(__('Este necesar un cont al unei banci romanesti.'));
                    }
                },
            ],
            'customer_reference' => ['nullable', 'string', 'max:30'],
            'assurance' => ['nullable', 'numeric', 'min:1'],
            'voucher' => ['nullable', 'min:1', 'exists:vouchers,code'],
        ];
    }

    public function serviceRules()
    {
        return [
            'curier' => ['required', 'string', 'max:255', Rule::exists(Curier::class, 'name')->where(function ($query) {
                foreach((new Curier)->getGlobalScopes() as $scope) {
                    $scope($query);
                }
                return $query;
            })],
        ];
    }

    public function allRules($noInvoiceInfo = true, $guest = false) 
    {
        return ($guest ? $this->guestEmailRules() : [])
            + $this->addressRules('sender')
            + $this->addressRules('receiver')
            + $this->packageRules()
            + ($noInvoiceInfo ? $this->addressRules('invoice') : [])
            + $this->serviceRules();
    }

    public function addressNames($type = null)
    {
        $type = in_array($type, ['sender','receiver','invoice']) ? $type.'.' : '';
        return [
            $type.'country' => __('tara'),
            $type.'name' => __('nume'),
            $type.'phone' => __('telefon'),
            $type.'phone_full' => __('telefon'),
            $type.'phone_2' => __('telefon 2'),
            $type.'phone_2_full' => __('telefon 2'),
            $type.'company' => __('companie'),
            $type.'email' => __('email'),
            $type.'postcode' => __('cod postal'),
            $type.'county' => __('judet'),
            $type.'locality' => __('localitate'),
            $type.'street' => __('strada'),
            $type.'street_nr' => __('nr. strada'),
            $type.'apartment' => __('apartament/nr. casa'),
            $type.'bl_code' => __('bloc'),
            $type.'bl_letter' => __('scara'),
            $type.'intercom' => __('interfon'),
            $type.'floor' => __('etaj'),
            $type.'landmark' => __('reper'),
            $type.'more_information' => __('informatii suplimentare'),
            $type.'is_company' => __('persoana'),
            $type.'cui_nif' => __('cui/nif'),
            $type.'nr_reg_com' => __('nr. reg. com.'),
            $type.'company_name' => __('nume companie'),
        ];
    }

    public function packageNames()
    {
        return [
            'type' => __('tip colet'),
            'nr_colete' => __('nr. colete'),
            'content' => __('continut'),
            'weight' => __('greutate colet'),
            'weight.*' => __('greutate'),
            'length' => __('lungime colet'),
            'length.*' => __('lungime'),
            'width' => __('latime colet'),
            'width.*' => __('latime'),
            'height' => __('inaltime colet'),
            'height.*' => __('inaltime'),
            'volume.*' => __('volum'),
            'awb' => __('AWB'),
            'pickup_day' => __('ziua ridicari'),
            'start_pickup_hour' => __('ora de inceput al ridicari'),
            'end_pickup_hour' => __('ora de sfarsit al ridicari'),
            'options.*' => __('optiune'),
            'options.retur_document' => __('retur documente/colete (SWAP)'),
            'swap_details.nr_parcels' => __('nr. colete pentru retur'),
            'swap_details.total_weight' => __('greutate totala pentru retur'),
            'ramburs' => __('ramburs'),
            'titular_cont' => __('nume titular cont'),
            'iban' => __('IBAN'),
            'customer_reference' => __('referinta client'),
            'assurance' => __('asigurare'),
        ];
    }

    public function allNames() 
    {
        return $this->addressNames('sender')
            + $this->addressNames('receiver')
            + $this->packageNames()
            + $this->addressNames('invoice');
    }

    public function trimPhoneNumberSpaces(Request $request)
    {
        $types = [null,'sender','receiver','invoice'];
        $keys = ['phone','phone_full','phone_2','phone_2_full'];
        $attr = Arr::dot($request->input());
        foreach($types as $type) {
            foreach($keys as $key) {
                $name = $type ? $type.'.'.$key : $key;
                if(isset($attr[$name]) && $attr[$name]) {
                    $attr[$name] = str_replace(' ', '', $attr[$name]);
                }
            }
        }
        return $request->merge(Arr::undot($attr));
    }

    public function replaceFullPhoneNumberRule($rules)
    {
        $types = [null,'sender','receiver','invoice'];
        $keys = ['phone','phone_2'];
        foreach($types as $type) {
            foreach($keys as $key) {
                $name = $type ? $type.'.'.$key : $key;
                if(isset($rules[$name.'_full'])) {
                    $rules[$name] = $rules[$name.'_full'];
                    unset($rules[$name.'_full']);
                }
            }
        }
        return $rules;
    }

    public function checkInput(Request $request)
    {
        $rules = [];
        $attributes = [];

        // Prepare rules
        $all_rules = $this->allRules(true, true) + $this->addressRules('*');
        foreach(Arr::dot($request->input()) as $name => $value) {
            if(isset($all_rules[$name])){
                $rules[$name] = $all_rules[$name];
            }
            $keys = explode('.', $name);
            $key = end($keys);
            if(in_array($key, ['postcode','country_code','county','locality','street','voucher','to_send_email'])) {
                $attributes[$key] = $value;
            }
            if(in_array($key, ['phone','phone_2'])) {
                $request->merge(Arr::undot([
                    $name => str_replace(' ', '', $request->input($name))
                ]));
            }
        }
        // END Prepare rules

        // Validation 
        $validated = Validator::make($request->input(), $rules, [], $this->allNames() + $this->addressNames());
        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()->all(), 'status' => 422]);
        }
        // END Validation

        // Get address or postcode if exists
        if(isset($attributes['country_code'])) {
            if(isset($attributes['postcode'])) {
                $query = CodPostal::select('localitate', 'judet', 'strada')
                    ->where('cod_postal', $attributes['postcode']);
                $return_value = 'address';
            } elseif(isset($attributes['county']) && isset($attributes['street'])) {
                $query = CodPostal::select('cod_postal')
                    ->where('judet', $attributes['county'])
                    ->where('localitate', $attributes['locality'])
                    ->where('strada', 'LIKE', '%'.($attributes['street'] ?? '').'%');
                $return_value = 'postcode';
            }
            if(isset($query) && $query->count() > 0) {
                return response()->json([$return_value => $query->first(), 'status' => 200]);
            }
        }

        // Get voucher if exists 
        if(isset($attributes['voucher'])) {
            $voucher = Voucher::firstWhere('code', $attributes['voucher']);
            return response()->json([
                'voucher' => $voucher != null ? [
                    'code' => $voucher->code,
                    'type' => $voucher->type,
                    'value' => $voucher->value,
                ] : null, 
                'status' => 200
            ]);
        } elseif(isset($attributes['to_send_email'])) {
            session()->put('to_send_email', isset($attributes['to_send_email']));
            return response()->json(['status' => 200]);
        }
        return true;
    }

    protected function phonePrefixes()
    {
        return cache()->remember('phone.prefixes', now()->addMonths(1), fn() => array_merge(Country::pluck('prefix')->toArray()), ['44']);
        // return ['32', '33', '34', '39', '40', '44', '49'];
    }

    protected function bankCodes()
    {
        return [
            'ABNA', 'ARBL', 
            'BCUN', 'BCYP', 'BITR', 'BLOM', 'BPOS', 'BRDE', 'BREL', 'BRMA', 'BSEA', 'BTRL', 'BUCU', 'BCRL', 'BACX',
            'CAIX', 'CARP', 'CECE', 'CITI', 'CRCO', 'CRDZ', 
            'DABA', 'DAFB', 'DARO', 'DPFA', 
            'EGNA', 'ETHN', 'EXIM', 
            'FNNB', 'FRBU', 'FTSB', 
            'HVBL', 
            'INGB', 
            'MILB', 'MIND', 'MIRO', 
            'NBOR', 
            'OTPV', 
            'PIRB', 'PORL', 
            'REVO', 'RNCB', 'ROIN', 'RZBL', 'RZBR', 
            'TRFD', 
            'UGBI', 
            'VBBU', 
            'WBAN', 
            'TREZ'
        ];
    }
}
