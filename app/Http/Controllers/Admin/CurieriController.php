<?php

namespace App\Http\Controllers\Admin;

use App\Courier\CourierGateway;
use App\Billing\PaymentGateway;
use App\Invoicing\InvoiceGateway;
use App\Models\Borderou;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\CodPostal;
use App\Models\Country;
use App\Models\Curier;
use App\Models\CurierDsicount;
use App\Models\Invoice;
use App\Models\Livrare;
use App\Traits\OrderCreationTrait;
use App\Traits\OrderStatusCheckTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PDF;

class CurieriController extends Controller
{
    use OrderCreationTrait;

    public function index()
    {
        return view('admin.curieri.show', [
            'curieri' => Curier::paginate(10),
        ]);
    }

    public function gatewaycall()
    {
        // $courierGateway = app(CourierGateway::class, ['type' => 1]);
        // $country = $courierGateway->findCountry(['isoAlpha2' => 'ro']);
        // $county = $courierGateway->findCounty(['countryId' => $country['CountryId'], 'name' => 'Suceava']);
        // $locality = $courierGateway->findLocality(['countryId' => $country['CountryId'], 'countyId' => $county['CountyId'], 'name' => 'Suceava']);
        // dd($county, $locality);
        // $result = $courierGateway->getLocationId([
        //     'name' => '',
        //     'countyId' => 37, // $county['CountyId'],
        //     'countyName' => 'Suceava',
        //     'localityId' => 170, // $locality['LocalityId'],
        //     'localityName' => 'Suceava',
        //     // 'StreetId' => null,
        //     'streetName' => 'Damaschin Mircea',
        //     'buildingNumber' => '',
        //     'address' => 'Address test',
        //     'contactPerson' => 'Test Contact Person',
        //     'phone' => '+4012123123',
        //     'email' => 'test@gmail.com',
        //     'postcode' => '720161'
        // ]);
        // dd($result);

        // $livrare = Livrare::find(215);
        // $livrare->pickup_day = now()->addDays(1)->format('Y-m-d');
        // $response = $this->newCreateOrder($livrare);
        // $borderou = Borderou::find(1713);
        // dd($borderou && $borderou->transformDate('end_date', 'Y-m-d') <= now()->format('Y-m-d') && $borderou->payed_at === null);
        // $paymentGateway = app(PaymentGateway::class, ['type' => 2]);
        // dd($paymentGateway->getToken([]));
        // dd($paymentGateway->setPaymentIntent([
        //     'amount' => 100,
        //     'creditor_name' => 'Test plata',
        //     'creditor_iban' => 'RO24BTRLRONCRT0445938101',
        //     'description' => __('Plata pentru borderoul #:id', ['id' => 3]),
        // ]));
        // $r = CodPostal::select('localitate')->groupBy('localitate')->count();
        // dd($r);
        // $response = preg_replace('/\s?\(.*\)/', '', 'Rasca (Suceava)');
        // dd(date('Y-m-d'), now()->format('Y-m-d'));
        // $invoiceGateway = app(InvoiceGateway::class, ['full_output' => true]);
        // dd($invoiceGateway->getToken());

        // $val = Validator::make(['asd' => 'a 123'], ['asd' => ['required', 'string', 'max:255', 'alpha_dash']]);
        // dd($val->fails());
    }

    public function create()
    {
        return view('admin.curieri.create', [
            'prices' => old('prices', $prices ?? []),
        ]);
    }

    public function store()
    {
        $input = Validator::make(request()->all(), $this->rules())->validate();

        if(request()->hasFile('logo'))
        {
            $file = request()->file('logo');
            $logo = Str::uuid() . "." . $file->getClientOriginalExtension();
            if($file->storeAs("img/curieri/", $logo, 'public')) {
                $input['logo'] = $logo;
            }
        }

        $nr_colete = $input['nr_colete'] ?? [];
 		unset($input['nr_colete']);
        $discounts = $input['discounts'] ?? [];
        unset($input['discounts']);
        $prices = $input['prices'] ?? [];
        unset($input['prices']);
        $special = $input['special'] ?? [];
        unset($input['special']);

        if($input['options']){
            foreach($input['options'] as $index => $value) {
                $input[$index] = $value;
            }
            unset($input['options']);
        }

        $curier = Curier::create($input);

        for($i = 0 ; $i < count($nr_colete) ; $i++) {
        	CurierDsicount::create([
        		'curier_id' => $curier->id,
        		'nr_colete' => $nr_colete[$i],
        		'discount' => $discounts[$i],
        	]);
        }

        if(count($special) > 0) {
            $curier->setMetas($special, 'special_');
        }

        $newPrices = [];
        foreach($prices['kg'] ?? [] as $index => $value) {
            $newPrices[$value] = $prices['price'][$index];
        }

        if(count($newPrices) > 0) {
            $curier->setMetas($newPrices, null, '_kg');
        }

        session()->flash('success', 'Curierul a fost creat cu succes.');
        return redirect()->route('admin.curieri.edit', $curier->id);
    }

    public function edit(Curier $curier)
    {
        return view('admin.curieri.create', [
            'curier' => $curier->withMetas('special_'),
            'prices' => old('prices', $this->unpluck($curier->pricesMetaWithKey('kg','price')) ?? []),
            'discounts' => $curier->discounts,
        ]);
    }

    public function unpluck($columns)
    {
        $result = [];
        foreach ($columns as $key => $column) {
            foreach ($column as $row => $value) {
                $result[$row][] = $value;
            }
        }
        return $result;
    }

    public function update(Curier $curier)
    {
        // dd(request()->all());
        $input = Validator::make(request()->all(), $this->rules($curier->id), [] ,$this->names())->validate();

        if(request()->hasFile('logo'))
        {
            $path = public_path('img/curieri/' . $curier->logo);
            if (file_exists($path)) {
                @unlink($path);
            }
            $file = request()->file('logo');
            $logo = Str::uuid() . "." . $file->getClientOriginalExtension();
            if($file->storeAs("img/curieri/", $logo, 'public')) {
                $input['logo'] = $logo;
            }
        }

        $nr_colete = $input['nr_colete'] ?? [];
        unset($input['nr_colete']);
        $discounts = $input['discounts'] ?? [];
        unset($input['discounts']);
        $prices = $input['prices'] ?? [];
        unset($input['prices']);
        $special = $input['special'] ?? [];
        unset($input['special']);

        $options = [
            'work_saturday',
            'require_awb',
            'open_when_received',
            'ramburs_cash',
            'ramburs_cont',
            'assurance',
        ];

        foreach($options as $index) {
            $input[$index] = null;
        }

        if($input['options']){
            foreach($input['options'] as $index => $value) {
                $input[$index] = $value;
            }
            unset($input['options']);
        }

        unset($input['old_logo']);

        Curier::where('id', $curier->id)->update($input);
        CurierDsicount::where('curier_id', $curier->id)->delete();
        for($i = 0 ; $i < count($nr_colete) ; $i++) {
        	CurierDsicount::create([
        		'curier_id' => $curier->id,
        		'nr_colete' => $nr_colete[$i],
        		'discount' => $discounts[$i],
        	]);
        }

        $curier->unsetMeta('special_%', 'like');
        if(count($special) > 0) {
            $curier->setMetas($special, 'special_');
        }

        $newPrices = [];
        foreach($prices['kg'] ?? [] as $index => $value) {
            $newPrices[$value] = $prices['price'][$index];
        }

        $curier->unsetMeta('%_kg', 'like');
        if(count($newPrices) > 0) {
            $curier->setMetas($newPrices, null, '_kg');
        }

        session()->flash('success', 'Curierul a fost modificat cu succes.');

        return redirect()->route('admin.curieri.edit', $curier->id);
    }

    public function destroy(Curier $curier)
    {
        $path = public_path('img/curieri/' . $curier->logo);
        if (file_exists($path)) {
            @unlink($path);
        }
        // UserMeta::where('user_id', $curier->id)->delete();
        Curier::where('id', $curier->id)->delete();

        session()->flash('success', 'Curierul a fost sters cu succes.');

        return redirect()->route('admin.curieri.show');
    }

    public function editRates(Curier $curier, User $user = null)
    {
        // dd($curier->rates->groupBy('country_id')->toArray());
        $rates = $user ? $user->rates($curier->id)->get() : $curier->rates;
        // dd($curier->rates->groupBy('country_id')->toArray(), $rates, $user);
        $rates = $rates->groupBy('country_id')->toArray();
        foreach($rates as $countryId => $rate) {
            $rates[$countryId] = $this->unpluck($rate);
        }
        return view('admin.curieri.rates', [
            'user' => $user,
            'curier' => $curier,
            'rates' => $rates,
            // 'prices' => old('prices', $this->unpluck($curier->rates)),
            'countries' => Country::all(),
            'countryPrices' => $user 
                ? $user->countryPrices($curier->id)->get()->keyBy('country_id')
                : $curier->countryPrices->keyBy('country_id'),
        ]);
    }

    public function updateRates(Request $request, Curier $curier, User $user = null)
    {
        $input = array_filter(Validator::make($request->all(), $this->rulesTarife($curier->id), [] ,$this->namesTarife())->validate());
        $rates = $input['rates'];
        $countries = collect($input['countries'])->only($rates)->toArray();
        // dd($countries);
        // dd($input);
        
        if($user) {
            $user->rates($curier->id)->delete();
            $user->countryPrices()->delete();
            $item = $user;
        } else {
            $curier->rates($curier->id)->delete();
            $curier->countryPrices()->delete();
            $item = $curier;
        }
        // dd($countries);
        foreach($countries as $countryId => $values) {
            $item->countryPrices()->updateOrCreate([
                'curier_id' => $curier->id,
                'user_id' => $user ? $user->id : null,
                'country_id' => $countryId
            ], collect($values)->except(['kg', 'price'])->toArray());

            $prices = collect($values)->only(['kg', 'price'])->toArray();
            // $prices = $this->unpluck(collect($values)->only(['kg', 'price'])->toArray());
            $newPrices = [];
            foreach($prices['kg'] ?? [] as $index => $value) {
                $newPrices[$index] = [
                    'curier_id' => $curier->id,
                    'user_id' => $user ? $user->id : null,
                    'country_id' => $countryId,
                    'weight' =>$prices['kg'][$index],
                    'price' => $prices['price'][$index]
                ];
            }
            // dd($newPrices, $prices);
            if($newPrices) {
                $item->rates()->createMany($newPrices);
            }
        }

        return redirect()->route('admin.curieri.edit.rates', [$curier->id, $user ? $user->id : null])->with([
            'success' => __('Tarife actualizate')
        ]);
    }

    public function rules($id = null)
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:curieri,name,'.$id],
            'api_curier' => ['required', Rule::in(['1','2','3'])],
            'type' => ['required', Rule::in(['1','2','3'])],
            'logo' => ['mimes:jpg,jpeg,png,gif,webp'] + (!$id ? ['required'] : [
                'nullable','required_unless:old_logo,1'
            ]),
            'old_logo' => [$id ? 'required' : 'nullable'],
            'tva' => ['required', 'integer', 'min:0', 'max:100'],
            'volum_price' => ['nullable', 'numeric', 'min:0'],
            // 'percent_price' => ['required', 'numeric', 'min:0', 'max:100'],
            // 'min_6kg_price' => ['nullable', 'numeric', 'min:0'],
            // 'minim_price' => ['nullable', 'numeric', 'min:0'],
            'special' => ['nullable', 'array', 'min:1'],
            'special.*' => ['nullable', 'numeric', 'min:1', 'max:100'],
            // 'prices' => ['nullable', 'array', 'min:1'],
            'prices.kg' => ['nullable', 'array', 'min:1'],
            'prices.price' => ['nullable', 'array', 'min:1', function ($attribute, $value, $fail) {
                if (count($value) != count(request()->input('prices.kg'))) {
                    $fail(__('The :attribute is invalid.'));
                }
            },],
            'prices.kg.*' => ['nullable', 'required_unless:prices.*,0', 'integer', 'min:1'],
            'prices.price.*' => ['nullable', 'required_unless:prices.*,0', 'numeric', 'min:1', 'max:100000'],
            'max_package_weight' => ['required', 'numeric', 'min:0'],
            'max_total_weight' => ['required', 'numeric', 'min:0'],
            'max_order_days' => ['required', 'numeric', 'min:1'],
            'performance_pickup' => ['required', 'numeric', 'min:1', 'max:5'],
            'performance_delivery' => ['required', 'numeric', 'min:1', 'max:5'],
            'discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'nr_colete.*' => ['nullable', 'integer', 'min:1', 'max:100'],
            'discounts.*' => ['nullable', 'numeric', 'min:0.00', 'max:100.00'],
            'last_order_hour' => ['required', 'integer', 'min:8', 'max:18'],
            'last_pick_up_hour' => ['required', 'integer', 'min:8', 'max:18'],
            'options.*' => ['required', Rule::in(['1'])],
            'office' => ['required', 'string', 'max:255'],
            'more_information' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function names()
    {
        return [
            'name' => __('nume'),
            'api_curier' => __('curier'),
            'type' => __('tip'),
            'logo' => __('logo'),
            'old_logo' => __('sigla actuala'),
            'tva' => __('tva'),
            'volum_price' => __('pret per kg'),
            // 'percent_price' => ['required', 'numeric', 'min:0', 'max:100'],
            // 'min_6kg_price' => ['nullable', 'numeric', 'min:0'],
            // 'minim_price' => __('pret minim'),
            'prices' => __('preturi minime'),
            'prices.*' => __('preturi minime'),
            'prices.*.kg' => __('nr. kg'),
            'prices.*.price' => __('pret minim'),
            'max_package_weight' => __('greutate maxima per colet'),
            'max_total_weight' => __('greutate maxima comanda'),
            'max_order_days' => __('numarul maxim de zile'),
            'performance_pickup' => __('performanta ridicare'),
            'performance_delivery' => __('performanta livrare'),
            'discount' => __('oferta'),
            'nr_colete.*' => __('nr. colete'),
            'discounts.*' => __('oferte'),
            'last_order_hour' => __('ora ultimei comenzi'),
            'last_pick_up_hour' => __('ora ultimei ridicari'),
            'options' => __('optiuni'),
            'options.*' => __('optiune'),
            'office' => __('sediu'),
            'more_information' => __('informatii'),
        ];
    }

    public function rulesTarife($id = null)
    {
        $checkValidCountry = fn($attribute) => in_array(explode('.', $attribute)[1], request()->input('rates.*'));
        $excludeInvalid = fn($attribute, $value, $fail) => Rule::excludeIf( !$checkValidCountry($attribute) );
        // $requiredValid = function($attribute, $value, $fail) { Rule::requiredIf( $checkValidCountry($attribute) ) };
        $requiredValid = function($attribute, $value, $fail) use ($checkValidCountry) {
            if(!$checkValidCountry($attribute)){
                $fail(__(':attribute este obligatoriu cand tara este selectata.'));
            }
        };
        return [
            'rates' => ['nullable', 'array', 'min:1'],
            'rates.*' => ['nullable', 'integer', 'min:1', Rule::exists(Country::class, 'id')],
            'countries' => ['nullable', 'array', 'min:1'],
            'countries.*' => ['nullable', 'array:volum_price,transa_ramburs,value_ramburs,percent_ramburs,kg,price', $excludeInvalid],
            'countries.*.volum_price' => ['nullable', 'numeric', 'min:0', $excludeInvalid],
            'countries.*.transa_ramburs' => ['nullable', 'numeric', 'min:0', $excludeInvalid],
            'countries.*.value_ramburs' => ['nullable', 'numeric', 'min:0', $excludeInvalid],
            'countries.*.percent_ramburs' => ['nullable', 'numeric', 'min:0', 'max:100', $excludeInvalid],
            'countries.*.kg' => ['nullable', 'array', 'min:1'],
            'countries.*.price' => ['nullable', 'array', 'min:1', function ($attribute, $value, $fail) {
                $countryId = explode('.', $attribute)[1];
                if (count($value) != count(request()->input('countries.'.$countryId.'.kg'))) {
                    $fail(__('The :attribute is invalid.'));
                }
            }],
            'countries.*.kg.*' => ['nullable', 'required_unless:countries.*.*,0', 'integer', 'min:1'],
            'countries.*.price.*' => ['nullable', 'required_unless:countries.*.*,0', 'numeric', 'min:1', 'max:100000'],
        ];
    }

    public function namesTarife()
    {
        return [
            'rates' => __('tarife'),
            'rates.*' => __('tarif'),
            'countries' => __('tari'),
            'countries.*' => __('tari'),
            'countries.*.volum_price' => __('pret per kg'),
            'countries.*.transa_ramburs' => __('transa ramburs'),
            'countries.*.value_ramburs' => __('adaos fix la ramburs'),
            'countries.*.percent_ramburs' => __('adaos procentual la ramburs'),
            'countries.*.kg' => __('kilogram minim per treapta'),
            'countries.*.price' => __('pret minim per treapta'),
            'countries.*.kg.*' => __('kilogram minim per treapta'),
            'countries.*.price.*' => __('pret minim per treapta'),
        ];
    }
}
