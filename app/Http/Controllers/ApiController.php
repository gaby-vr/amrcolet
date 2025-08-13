<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Courier\CourierGateway;
use App\Models\Address;
use App\Models\CodPostal;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Curier;
use App\Models\Livrare;
use App\Models\Invoice;
use App\Models\InvoiceMeta;
use App\Models\Package;
use App\Models\Repayment;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use App\Traits\OrderCreationTrait;
use App\Traits\OrderValidationTrait;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use View;
use Log;
use PDF;
use DB;
use PostalCode;

class ApiController extends Controller
{
    use OrderCreationTrait, OrderValidationTrait;

    public function check(Request $request)
    {
        $rules = self::settingRules();
        $attributeNames = self::inputNames();

        $validated = Validator::make($request->input(), $rules);
        $validated->setAttributeNames($attributeNames);
        if ($validated->stopOnFirstFailure()->fails()) {
            return response()->json(['error' => $validated->messages()->first(), 'status' => 422]);
        }

        if($request->hasHeader('Key')) {

            $key = $request->header('Key');
            $user = User::where('id', function($query) use($key) {
                $query->select('user_id')
                    ->from('user_metas')
                    ->where('user_metas.name', 'wordpress_api_key')
                    ->where('user_metas.value', $key)
                    ->distinct('user_metas.user_id')
                    ->whereColumn('user_metas.user_id', 'users.id');
            })->first();

            if($user != null) {
                // Log::info('Host test');
                // Log::info('Host: '.self::getHost($user->meta('wordpress_domain')));
                // Log::info('Referer: '.self::getHost($request->header('Referer')));
                // Log::info('Origin: '.self::getHost($request->header('Origin')));
                if(self::getHost($user->meta('wordpress_domain')) == self::getHost($request->header('Origin'))) {

                    if(
                        !($user->role == '2' 
                            && $user->meta('expiration_date') != '' 
                            && $user->meta('expiration_date') <= date('Y-m-d'))
                    ) {
                        return response()->json([
                            'message' => __('Informatiile au fost salvate.'), 
                            'status' => 200 
                        ] + (
                            $request->has('waiting_approval')
                            && $request->input('waiting_approval') == '1'
                            && $user->favoriteAddresses()->count() > 0
                            ? [
                                'addresses' => $user->favoriteAddresses()->select('id','address_name')->get()->toArray()
                            ] : []
                        ));
                    } else {
                        return response()->json(['error' => __('Perioada de comanda fara plata s-a terminat.<br>Plateste comenzile neachitate pentru a continua.'), 'status' => 422]);
                    }
                } else {
                    return response()->json(['error' => __('Domeniul este gresit.'), 'status' => 422]);
                }
            } else {
                return response()->json(['error' => __('Cheia API este gresita.'), 'status' => 422]);
            }
        } else {
            return response()->json(['error' => __('Cheia API este absenta.'), 'status' => 422]);
        }
        return response()->json(['error' => __('Eroare! Incercati mai tarziu.'), 'status' => 422]);
    }

    public function store(Request $request)
    {
        if($request->hasHeader('Key'))
        {
            $key = $request->header('Key');
            $user = User::where('id', function($query) use($key) {
                $query->select('user_id')
                    ->from('user_metas')
                    ->where('user_metas.name', 'wordpress_api_key')
                    ->where('user_metas.value', $key)
                    ->distinct('user_metas.user_id')
                    ->whereColumn('user_metas.user_id', 'users.id');
            })->first();
            if($user != null && self::getHost($user->meta('wordpress_domain')) == self::getHost($request->header('Origin'))) {

                if(!($user->role == '2' && $user->meta('expiration_date') != '' && $user->meta('expiration_date') < date('Y-m-d')))
                {
                    if($user->metas()->select('name','value')->where('name', 'like', 'invoice_%')->count() > 0)
                    {
                        try {
                            // Validation 

                            $rules = self::allRules($user->id);
                            $attributeNames = self::inputNames();
                            // Log::info('Api attr: '.$user->id);
                            // Log::info($request->input());

                            $validated = Validator::make($request->input(), $rules);
                            $validated->setAttributeNames($attributeNames);

                            if($validated->fails()) {
                                return response()->json(['error' => $validated->messages()->first(), 'status' => 422]);
                            }

                            $attributes = $validated->validate();

                            // END Validation

                            $sender = isset($attributes['address_id']) 
                                ? Address::find($attributes['address_id'])->toArray()
                                : $request->input('sender');
                            $receiver = $request->input('receiver');
                            $livrare = $request->input('packages');
                            
                            if($livrare['pickup_day'] > 0) {
                                $livrare['pickup_day'] = now()->addDays($livrare['pickup_day']);
                            } elseif(
                                time() >= strtotime("15:00:00") 
                                || $livrare['end_pickup_hour'] < now()->addHours(2)->format('G')
                            ) {
                                $livrare['pickup_day'] = now()->addDays(1);
                            } else {
                                $livrare['pickup_day'] = date('Y-m-d');
                            }

                            $package = [];
                            if($livrare['type'] == '1' && $livrare['nr_colete'] > 0) {
                                $livrare['total_volume'] = 0;
                                $livrare['total_weight'] = 0;
                                $weight = true;
                                $dimensions = true;
                                for($i = 0 ; $i < $livrare['nr_colete'] ; $i++)
                                {
                                    if(isset($livrare['weight'][$i]) && $livrare['weight'][$i] != null) {
                                        $package[$i]['weight'] = $livrare['weight'][$i];
                                        $livrare['total_weight'] += $livrare['weight'][$i];
                                    } else {
                                        $weight = false;
                                    }

                                    if(isset($livrare['width'][$i]) && $livrare['width'][$i] != null && isset($livrare['length'][$i]) && $livrare['length'][$i] != null && isset($livrare['height'][$i]) && $livrare['height'][$i] != null) {
                                        $package[$i]['width'] = $livrare['width'][$i];
                                        $package[$i]['length'] = $livrare['length'][$i];
                                        $package[$i]['height'] = $livrare['height'][$i];
                                        $package[$i]['volume'] = round(($package[$i]['width'] * $package[$i]['length'] * $package[$i]['height'])/6000, 2);

                                        $livrare['total_volume'] += $package[$i]['volume'];
                                    } else {
                                        $dimensions = false;
                                    }

                                    if(($weight == false && $dimensions == false) || $weight < 1 || $dimensions < 1) {
                                        $livrare['total_volume'] += 1;
                                        $livrare['total_weight'] += 1;
                                        $package[$i]['width'] = 10;
                                        $package[$i]['length'] = 10;
                                        $package[$i]['height'] = 10;
                                        $package[$i]['volume'] = 1;
                                        $package[$i]['weight'] = 1;
                                        // return response()->json(['error' => __('Greutatea sau dimensiunile nu au fost specificate.'), 'status' => 422]);
                                    }
                                }
                            }
                            // Log::info($livrare);

                            // sender        
                            $sender['type'] = 1;
                            $sender['county'] = remove_accents($sender['county']);
                            $sender['locality'] = remove_accents($sender['locality']);

                            // receiver
                            $receiver['type'] = 2;
                            $receiver['county'] = remove_accents($receiver['county']);
                            $receiver['locality'] = remove_accents($receiver['locality']);

                            $getCurier = self::getCurier($livrare, $receiver, $sender, $user);
                            // \Log::info('Curier');
                            // \Log::info($getCurier);

                            if($getCurier['curier'] !== false) {

                                $curier = Curier::firstWhere('id', $getCurier['curier']);
                                $normalPrice = $getCurier['min'];
                                if(isset($livrare['nr_colete']) && $livrare['nr_colete'] > 0) {
                                    $price = $normalPrice - $normalPrice * ($curier->discount($livrare['nr_colete'])/100);
                                } else {
                                    $price = $normalPrice;
                                }
                                $price = round($price, 2);
                                
                                $livrare['curier'] = $curier->name;
                                $livrare['api'] = $curier->api_curier;

                                $livrare['original_value'] = $price;

                                if(isset($livrare['voucher'])) {
                                    $voucher = Voucher::firstWhere('code', $livrare['voucher']);

                                    $livrare['value'] = $voucher->type == '1' 
                                        ? ($livrare['original_value'] - $voucher->value) 
                                        : round(($livrare['original_value'] - ($livrare['original_value'] * $voucher->value/100)), 2);
                                    $livrare['voucher_code'] = $voucher->code;
                                    $livrare['voucher_type'] = $voucher->type;
                                    $livrare['voucher_value'] = $voucher->value;
                                } else {
                                    $livrare['value'] = $livrare['original_value'];
                                }

                                if(isset($livrare['options'])) {
                                    foreach($livrare['options'] as $key => $value) {
                                        if($value != 0) {
                                            $livrare[$key] = $value;
                                        }
                                    }
                                }

                                $amount = $livrare['value'];
                                
                                $livrare['email'] = $user->email;
                                $livrare['user_id'] = $user->id;
                                    
                                if($user->meta('account_balance') >= $livrare['value']) {
                                    $user->setMeta('account_balance', $user->meta('account_balance') - $livrare['value']);
                                    $livrare['nr_credits_used'] = $livrare['value'];
                                    $amount = 0;
                                } else {
                                    // check if the user is a contractant (a user who has a contract with the admin(owner))
                                    if($user->role == '2') {
                                        if($user->meta('account_balance') != '') {
                                            if($user->meta('account_balance') > 0) {
                                                $livrare['nr_credits_used'] = $user->meta('account_balance');
                                            }
                                            $user->setMeta('account_balance', $user->meta('account_balance') - $livrare['value']);
                                        } else {
                                            $user->setMeta('account_balance', -1 * $livrare['value']);
                                        }
                                        $livrare['payed'] = 0;
                                        if($user->meta('expiration_date') == '') {
                                            $user->setMeta('expiration_date', now()->addDays($user->meta('days_of_negative_balance'))->format('Y-m-d'));
                                        }
                                        $amount = 0;
                                    } else {
                                        return response()->json(['error' => __('Nu mai sunt destule credite in cont pentru a putea efectua comanda.'), 'status' => 422]);
                                    }
                                }

                                $livrare = Livrare::create($livrare);

                                $sender['livrare_id'] = $livrare->id;
                                $sender = Contact::create($sender);

                                $receiver['livrare_id'] = $livrare->id;
                                $receiver = Contact::create($receiver);

                                if($livrare->type == '1') {
                                    for($i = 0 ; $i < $livrare->nr_colete ; $i++) {
                                        $package[$i]['type'] = 1;
                                        $package[$i]['livrare_id'] = $livrare->id;
                                        $package[$i]['template_id'] = 0;
                                        Package::create($package[$i]);
                                    }
                                }

                                self::createOrder($livrare);
                                return response()->json(['message' => __('Comanda #:livrare a fost trimisa curierului. <br>Costul total este de :pret. <br> Codul AWB de identificare este :cod.', ['livrare' => $livrare->id, 'pret' => $livrare->value, 'cod' => $livrare->api_shipment_awb ]), 'status' => 200]);

                            } else {
                                return response()->json(['error' => __('Nu a fost gasit nici un curier.'), 'status' => 422]);
                            }
                        } catch(\Exception $e) {
                            \Log::info($e->getMessage());
                            \Log::info('The exception was created on line: ' . $e->getLine());
                            return response()->json(['error' => __('Ruta API nu mai este valida.'), 'status' => 422]);
                        }
                        
                    } else {
                        return response()->json(['error' => __('Nu aveti completate informatiile de facturare.'), 'status' => 422]);
                    }
                } else {
                    return response()->json(['error' => __('Perioada de comanda fara plata s-a terminat.<br>Plateste comenzile neachitate pentru a continua.'), 'status' => 422]);
                }
            } else {
                return response()->json(['error' => __('Cheia API sau domeniul este gresit.'), 'status' => 422]);
            }
        } else {
            return response()->json(['error' => __('Cheia API este absenta.'), 'status' => 422]);
        }
    }

    public function getHost($address)
    { 
        $parseUrl = parse_url(trim($address));
        $path = isset($parseUrl['path']) ? $parseUrl['path'] : '';
        $host = isset($parseUrl['host']) ? $parseUrl['host'] : '';
        if(trim(!empty($host))) {
            return $host;
        } else {
            $path = explode('/', $path, 2);
            return array_shift($path);
        }
    }

    public function replaceSpecialChars($string)
    { 
        $parseUrl = parse_url(trim($address));
        $path = isset($parseUrl['path']) ? $parseUrl['path'] : '';
        $host = isset($parseUrl['host']) ? $parseUrl['host'] : '';
        if(trim(!empty($host))) {
            return $host;
        } else {
            $path = explode('/', $path, 2);
            return array_shift($path);
        }
    } 

    public function getCurier($package, $receiver, $sender, $user = null) 
    {
        $result = $this->calculateConditionsAndPracels($package);
        $conditions = $result['conditions'];
        $parcels = $result['parcels'];

        $curieri = Curier::where('max_package_weight', '>=', $conditions['max_weight'])
                ->where('max_total_weight', '>=', $conditions['total_weight'])
                ->where('last_pick_up_hour', '>=', $package['start_pickup_hour'])
                ->where('api_curier', 2);
                //->whereBetween('last_pick_up_hour', [$package['start_pickup_hour'], $package['end_pickup_hour']]);

        if(isset($package['options'])) {
            foreach($package['options'] as $key => $value) {
                $curieri->where($key, '=', 1);
            }
        }

        if($package['ramburs'] == '2') {
            $curieri->where('ramburs_cash', '=', 1);
        } elseif($package['ramburs'] == '3') {
            $curieri->where('ramburs_cont', '=', 1);
        }

        if($package['awb'] == '2') {
            $curieri->where('require_awb', '=', 1);
        }

        $total = [];
        $min = 100000;
        $curierMin = false;
        // \Log::info('Curier get');
        // \Log::info($conditions);
        // \Log::info($package);
        // \Log::info($curieri->get());
        foreach($curieri->get() as $curier) 
        {
            $total[$curier->id] = isset($total[$curier->id]) 
                ? $total[$curier->id] 
                : self::getCurierPrice($curier, $package, $sender, $receiver, $conditions, $parcels, $user);
            $value = $total[$curier->id] != false ? $total[$curier->id]['total_price'] : false;
            // \Log::info('Curier preturi api');
            // \Log::info($curier->id);
            // \Log::info($value);
            if($value != false && $value < $min) {
                $min = $value;
                $curierMin = $curier->id;
            }
        }

        return [
            'min' => $min,
            'curier' => $curierMin,
        ];
    }

    public function calculateConditionsAndPracels($package)
    {
        $parcels = [];
        $conditions['max_weight'] = 0;
        $conditions['total_volume'] = 0;
        $conditions['total_weight'] = 0;
        
        if( $package['nr_colete'] > 0 && $package['type'] == '1') 
        {
            for($i = 0 ; $i < $package['nr_colete'] ; $i++)
            {
                $parcels[$i]['id'] = $i + 1;
                $parcels[$i]['weight'] = $package['weight'][$i] ?? 1;
                $parcels[$i]['width'] = $package['width'][$i] ?? 10;
                $parcels[$i]['length'] = $package['length'][$i] ?? 10;
                $parcels[$i]['height'] = $package['height'][$i] ?? 10;

                $conditions['max_weight'] = $conditions['max_weight'] < $package['weight'][$i] ? $package['weight'][$i] : $conditions['max_weight'];
                $conditions['total_volume'] += round(($package['width'][$i] * $package['length'][$i] * $package['height'][$i])/6000, 2);
                $conditions['total_weight'] += $package['weight'][$i];
            }
            $conditions['calc_weight'] = $conditions['total_weight'] > $conditions['total_volume'] ? $conditions['total_weight'] : $conditions['total_volume'];
        } else {
            $conditions['total_weight'] = 1;
            $conditions['calc_weight'] = 1;
        }
        if($conditions['total_weight'] < 1) {
            $conditions['total_weight'] = 1;
        }

        if($conditions['calc_weight'] < 1) {
            $conditions['calc_weight'] = 1;
        }
        if($conditions['total_volume'] < 1) {
            $conditions['total_volume'] = 1;
        }
        if($conditions['max_weight'] < 1) {
            $conditions['max_weight'] = 1;
        }
        return [
            'conditions' => $conditions,
            'parcels' => $parcels,
        ];
    }

    public function getCurierPrice(Curier $curier, $package, $sender, $receiver, $newConditions = null, $newParcels = null, $user = null) 
    {
        if($newConditions == null && $newParcels == null) {
            $result = $this->calculateConditionsAndPracels($package);
            $conditions = $result['conditions'];
            $parcels = $result['parcels'];
        } else {
            $parcels = $newParcels;
            $conditions = $newConditions;
        }

        $total = [
            'total_price' => false,
            'api_price' => false,
        ];

        // \Log::info('Curier');
        // \Log::info($curier->api_curier);

        switch ($curier->api_curier) {
            case 1: // Urgent Cargus
                if($package['nr_colete'] > 1) {
                    return false;
                }
                try {
                    $api = app(CourierGateway::class, ['type' => $curier->api_curier]);
                    $fromCountry = $api->findCountry(['isoAlpha2' => $sender['country_code'] ?? 'ro']);
                    if($sender['country_code'] == $receiver['country_code']) {
                        $toCountry = $fromCountry;
                    } else {
                        $toCountry = $api->findCountry(['isoAlpha2' => $receiver['country_code']]);
                    }

                    if($fromCountry) {
                        $fromCounty = $api->findCounty(['countryId' => $fromCountry['CountryId'], 'name' => $sender['county']]);
                    } else { return false; }
                    if($toCountry) {
                        $toCounty = $api->findCounty(['countryId' => $toCountry['CountryId'], 'name' => $receiver['county']]);
                    } else { return false; }
                    if($fromCounty) {
                        $from = $api->findLocality(['countryId' => $fromCountry['CountryId'], 'countyId' => $fromCounty['CountyId'], 'name' => $sender['locality']]);
                    } else { return false; }
                    if($toCounty) {
                        $to = $api->findLocality(['countryId' => $toCountry['CountryId'], 'countyId' => $toCounty['CountyId'], 'name' => $receiver['locality']]);
                    } else { return false; }

                    $price = $api->calculateOrder([
                        'from' => [
                            'localityId' => $from['LocalityId'] ?? 0,
                            'countyName' => $fromCounty['Name'] ?? $sender['county'],
                            'localityName' => $from['Name'] ?? $sender['locality'],
                        ],
                        'to' => [
                            'localityId' => $to['LocalityId'] ?? 0,
                            'countyName' => $toCounty['Name'] ?? $receiver['county'],
                            'localityName' => $to['Name'] ?? $receiver['locality'],
                        ],
                        'parcels' => $package['nr_colete'] ?? 0,
                        'envelops' => isset($package['nr_colete']) && $package['nr_colete'] > 0 ? 0 : 1,
                        'TotalWeight' => round($conditions['calc_weight']) ?? 1,
                        'DeclaredValue' => $package['assurance'] ?? 0,
                        'CashRepayment' => isset($package['ramburs']) && $package['ramburs'] == '2' 
                            ? ($package['ramburs_value'] ?? 0) : 0,
                        'BankRepayment' => isset($package['ramburs']) && $package['ramburs'] == '3' 
                            ? ($package['ramburs_value'] ?? 0) : 0,
                        'OtherRepayment' => '',
                        'OpenPackage' => isset($package['options']['open_when_received']) ? true : false,
                        'MorningDelivery' => false,
                        'SaturdayDelivery' => isset($package['options']['work_saturday']) && isset($to['SaturdayDelivery']) ? $to['SaturdayDelivery'] : false,
                        'PackageContent' => $package['content'],
                    ]); // return GrandTotal

                    // $total['api_price'] = false;
                    // $total['api_price'] = 19;
                    // $total['api_price'] = ($price != false && isset($price['GrandTotal'])) ? $price['GrandTotal'] : false;
                    // \Log::info('Cargus');
                    // \Log::info($price);
                    $total['api_price'] = ($price != false && isset($price['GrandTotal'])) 
                        ? ($price['GrandTotal'] < 19 ? 19 : $price['GrandTotal']) 
                        : false;
                    
                } catch(\Exception $e) {
                    \Log::info($e);
                }
                
                break;
            case 2: // DPD
                if($package['nr_colete'] > 10) {
                    return false;
                } elseif(($sender['country_code'] != 'ro' || $receiver['country_code'] != 'ro') && $package['nr_colete'] > 1) {
                    return false;
                }
                try {
                    $api = app(CourierGateway::class, ['type' => $curier->api_curier]);

                    // sender
                    $senderCountry = $api->findCountry(['isoAlpha2' => $sender['country_code'] ?? 'ro']);
                    // \Log::info('Dpd sender');
                    // \Log::info($senderCountry);
                    if(!$senderCountry) {
                        return false;
                    }
                    $senderSite = false;
                    if($senderCountry && $senderCountry['requireState'] == true) {
                        $senderState = $api->findState(['countryId' => $senderCountry['id'], 'name' => '']);
                    }
                    // if($senderCountry && $senderCountry['siteNomen'] > 0) {
                    //     $senderSite = $api->findSite(['countryId' => $senderCountry['id'], 'postCode' => $sender['postcode'], 'region' => $sender['county']]);
                    // }
                    // receiver
                    $receiverCountry = $api->findCountry(['isoAlpha2' => $receiver['country_code'] ?? 'ro']);
                    // \Log::info('Dpd receiver');
                    // \Log::info($receiverCountry);
                    if(!$receiverCountry) {
                        return false;
                    }
                    $receiverSite = false;
                    if($receiverCountry && $receiverCountry['requireState'] == true) {
                        $receiverState = $api->findState(['countryId' => $receiverCountry['id'], 'name' => '']);
                    }
                    // if($receiverCountry && $receiverCountry['siteNomen'] > 0) {
                    //     $receiverSite = $api->findSite(['countryId' => $receiverCountry['id'], 'postCode' => $receiver['postcode'], 'region' => $receiver['county']]);
                    // }

                    $price = $api->calculateOrder([
                        'parcels' => $parcels,
                        'senderPrivatePerson' => $sender['company'] ?? '',
                        'senderCountryId' => $senderCountry['id'],
                        'senderSiteType' => $senderCountry['siteNomen'],
                        'senderSiteName' => $sender['county'],
                        'senderPostCode' => $sender['postcode'],
                        'receiverPrivatePerson' => $receiver['company'] ?? '',
                        'receiverCountryId' => $receiverCountry['id'],
                        'receiverSiteType' => $receiverCountry['siteNomen'],
                        'receiverSiteName' => $receiver['county'],
                        'receiverPostCode' => $receiver['postcode'],
                        'parcelsCount' => $package['nr_colete'] ?? 1,
                        'totalWeight' => $conditions['calc_weight'] ?? 1,
                        'pickupDate' => $package['pickup_day'] instanceof Carbon 
                            ? $package['pickup_day']->format('Y-m-d') 
                            : $package['pickup_day'],
                        'saturdayDelivery' => $package['options']['work_saturday'] ?? false,
                        'declaredValue' => $package['assurance'] ?? '',
                        'ramburs' => $package['ramburs'],
                        'rambursValue' => $package['ramburs_value'] ?? 0,
                        'iban' => $package['iban'] ?? '',
                        'accountHolder' => $package['titular_cont'] ?? '',
                        'obpd' => $package['options']['open_when_received'] ?? '',
                        'swap' => $package['options']['retur_document'] ?? '',
                        'swap_parcels' => $package['swap_details']['nr_parcels'] ?? '',
                        'contents' => $package['content'],
                        'serviceId' => $receiver['country_code'] != 'ro'
                            ? $api->getServiceCode($receiver['country_code'] ?? 'ro')
                            : $api->getServiceCode($sender['country_code'] ?? 'ro', false),
                        'currencyCode' => $receiver['country_code'] != 'ro'
                            ? Country::code($receiver['country_code'] ?? 'ro')->currency_code
                            : Country::code($sender['country_code'] ?? 'ro')->currency_code,
                    ]);

                    // \Log::info('Dpd');
                    // \Log::info($price);
                    $total['api_price'] = ($price != false && isset($price['price'])) ? $price['price']['total'] : false;
                
                } catch(\Exception $e) {
                    \Log::info($e);
                }

                break;
            default:
                return $total;
        }

        if($total['api_price']) {
            $receiver['country_code'] = $receiver['country_code'] ?? 'ro';
            $sender['country_code'] = $sender['country_code'] ?? 'ro';
            $total['total_price'] = $curier->calculatePriceForConditions(
                $package, $conditions['calc_weight'], $user, $parcels, null,
                $receiver['country_code'] !== 'ro' ? $receiver['country_code'] : ($sender['country_code'] !== 'ro' ? $sender['country_code'] : null)
            );
            // $value = $curier->minimPriceForKg($conditions['calc_weight']);
            // $added = isset($package['assurance']) && $package['assurance'] > 0 
            //     ? $package['assurance'] * $curier->percent_assurance/100
            //     : 0;
            // $total['total_price'] = $value + $added;
            // if(
            //     isset($package['ramburs']) && $package['ramburs'] == '3'
            //     && isset($package['ramburs_value']) && $package['ramburs_value'] > 0
            // ) {
            //     $total['total_price'] = $curier->addRambursValue($total['total_price']);
            // }
            // if(isset($package['options']['open_when_received']) && $package['options']['open_when_received'] > 0) {
            //     $total['total_price'] += $curier->value_owr;
            // }
            // if(isset($package['options']['retur_document']) && $package['options']['retur_document'] > 0) {
            //     $total['total_price'] += $curier->minimPriceForKg($package['swap_details']['total_weight'] ?? 1, $user);
            // }

        }

        if(!$total['total_price']) {
            $total['total_price'] = $total['api_price'];
        }

        return $total;
    }

    public function settingRules() 
    {
        return [
            'waiting_approval' => ['nullable', 'min:1', 'max:1', 'integer'],
            'type' => ['required', 'min:1', 'max:2', 'integer'],
            'content' => ['required', 'string', 'max:50'],
            'weight.*' => ['nullable', 'required_if:type,1', 'numeric', 'min:0.001', 'max:200'],
            'length.*' => ['nullable', 'required_if:type,1', 'numeric', 'min:1', 'max:200'],
            'width.*' => ['nullable', 'required_if:type,1', 'numeric', 'min:1', 'max:200'],
            'height.*' => ['nullable', 'required_if:type,1', 'numeric', 'min:1', 'max:200'],
            'volume.*' => ['nullable', 'required_if:type,1', 'numeric', 'min:0.001', 'max:200'],
            'awb' => ['required', 'min:1', 'max:2', 'integer'],
            'pickup_day' => ['required', 'integer', 'min:0', 'max:2'],
            'start_pickup_hour' => ['required', 'integer', 'min:8', 'max:18'],
            'end_pickup_hour' => ['required', 'integer', 'min:9', 'max:18'],
            'options.*' => ['nullable', 'max:1'],
            'ramburs' => ['required', 'integer', 'min:1', 'max:3'],
            // 'ramburs_value' => ['nullable', 'required_unless:ramburs,1', 'numeric', 'min:1', 'max:1000000'],
            'titular_cont' => ['nullable', 'required_if:ramburs,3', 'string', 'min:1', 'max:32'],
            'iban' => ['nullable', 'required_if:ramburs,3', 'string', 'min:1', 'max:50'],
            'customer_reference' => ['nullable', 'string', 'max:50'],
            'assurance' => ['nullable', 'numeric', 'min:0'],
            'voucher' => ['nullable', 'min:1', 'exists:vouchers,code'],
        ];
    }

    public function allRules($user_id) 
    {
        return [
            'address_id' => ['nullable', 'integer', 'min:1',
                Rule::exists(Address::class, 'id')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                }),
            ],
            'sender.country' => ['nullable', 'required_without:address_id', 'string', 'max:60'],
            'sender.country_code' => ['nullable', 'required_without:address_id', 'string', 'min:1', 'max:5'],
            'sender.name' => ['nullable', 'required_without:address_id', 'string', 'min:3', 'max:60'],
            'sender.phone' => ['nullable', 'required_without:address_id', 'string', 'min:5', 'max:20'],
            //'sender.phone_full' => ['required', 'string', 'min:1', 'max:20'],
            'sender.phone_2' => ['nullable', 'string', 'min:5', 'max:20'],
            //'sender.phone_2_full' => ['nullable', 'string', 'min:1', 'max:20'],
            'sender.company' => ['nullable', 'string', 'min:3', 'max:60'],
            'sender.email' => ['nullable', 'required_without:address_id', 'string', 'email:strict,dns', 'min:3', 'max:60'],
            'sender.postcode' => ['nullable', 'string', 'min:3', 'max:15', 
                PostalCode::for(strtoupper(request()->input('sender.country_code'))), 
                function ($attribute, $value, $fail) {
                    if (
                        request()->has('sender.country_code') 
                        && request()->input('sender.country_code') == 'ro'
                    ) {
                        if(CodPostal::where('cod_postal', $value)->count() < 1) {
                            $fail('Campul cod postal este gresit.');
                        }
                    }
                },
            ],
            'sender.county' => ['nullable', 'required_without:address_id', 'string', 'min:3', 'max:60'],
            'sender.locality' => ['nullable', 'required_without:address_id', 'string', 'min:3', 'max:60'],
            'sender.street' => ['nullable', 'required_without:address_id', 'string', 'max:50'],
            'sender.street_nr' => ['nullable', 'string', 'max:50'],
            'sender.apartment' => ['nullable', 'string', 'max:60'],
            'sender.bl_code' => ['nullable', 'string', 'max:60'],
            'sender.bl_letter' => ['nullable', 'string', 'max:60'],
            'sender.intercom' => ['nullable', 'string', 'max:60'],
            'sender.floor' => ['nullable', 'string', 'max:60'],
            'sender.landmark' => ['nullable', 'string', 'max:60'],
            'sender.more_information' => ['nullable', 'string', 'max:60'],

            'receiver.country' => ['required', 'string', 'max:60'],
            'receiver.country_code' => ['required', 'string', 'min:1', 'max:5'],
            'receiver.name' => ['required', 'string', 'min:3', 'max:60'],
            'receiver.phone' => ['required', 'string', 'min:5', 'max:20'],
            //'receiver.phone_full' => ['required', 'string', 'min:1', 'max:20'],
            'receiver.phone_2' => ['nullable', 'string', 'min:5', 'max:20'],
            //'receiver.phone_2_full' => ['nullable', 'string', 'min:1', 'max:20'],
            'receiver.company' => ['nullable', 'string', 'min:3', 'max:60'],
            'receiver.email' => ['nullable', 'string', 'email:strict,dns', 'min:3', 'max:60'],
            'receiver.postcode' => ['nullable', 'string', 'min:3', 'max:15', 
                PostalCode::for(strtoupper(request()->input('receiver.country_code'))),
                function ($attribute, $value, $fail) {
                    if (
                        request()->has('receiver.country_code') 
                        && request()->input('receiver.country_code') == 'ro'
                    ) {
                        if(CodPostal::where('cod_postal', $value)->count() < 1) {
                            $fail('Campul cod postal este gresit.');
                        }
                    }
                },
            ],
            'receiver.county' => ['required', 'string', 'min:3', 'max:60'],
            'receiver.locality' => ['required', 'string', 'min:3', 'max:60'],
            'receiver.street' => ['nullable', 'required_without:address_id', 'string', 'max:50'],
            'receiver.street_nr' => ['nullable', 'string', 'max:50'],
            'receiver.apartment' => ['nullable', 'string', 'max:60'],
            'receiver.bl_code' => ['nullable', 'string', 'max:60'],
            'receiver.bl_letter' => ['nullable', 'string', 'max:60'],
            'receiver.intercom' => ['nullable', 'string', 'max:60'],
            'receiver.floor' => ['nullable', 'string', 'max:60'],
            'receiver.landmark' => ['nullable', 'string', 'min:1', 'max:60'],
            'receiver.more_information' => ['nullable', 'string', 'min:1', 'max:60'],

            'packages.type' => ['required', 'min:1', 'max:2', 'integer'],
            'packages.nr_colete' => ['nullable', 'exclude_if:packages.type,2', 'required_if:packages.type,1', 'integer', 'min:1'],
            'packages.content' => ['required', 'string', 'max:50'],
            'packages.weight.*' => ['nullable', 'required_if:type,1', 'integer', 'min:1', 'max:200'],
            'packages.length.*' => ['nullable', 'required_if:type,1', 'integer', 'min:1', 'max:200'],
            'packages.width.*' => ['nullable', 'required_if:type,1', 'integer', 'min:1', 'max:200'],
            'packages.height.*' => ['nullable', 'required_if:type,1', 'integer', 'min:1', 'max:200'],
            'packages.awb' => ['required', 'min:1', 'max:2', 'integer'],
            'packages.pickup_day' => ['required', 'integer', 'min:0', 'max:2'],
            'packages.start_pickup_hour' => ['required', 'integer', 'min:8', 'max:18'],
            'packages.end_pickup_hour' => ['required', 'integer', 'max:18', 
                'min:'.(request()->has('start_pickup_hour') 
                    ? (int)request()->has('start_pickup_hour') + 2 
                    : '9'),

                function ($attribute, $value, $fail) {
                    if (
                        request()->has('pickup_day') 
                        && request()->input('pickup_day') == now()->format('Y-m-d')
                        && $value < now()->addHours(2)->format('H')
                    ) {
                        $fail(__('Campul :attribute trebuie sa fie cu minim 2 ore in viitor de la ora curenta daca doresti ca aceasta sa fie efectuata astazi.'));
                    }
                },
            ],
            'packages.options.*' => ['nullable', 'max:1'],
            'packages.ramburs' => ['required', 'integer', 'min:1', 'max:3'],
            // 'ramburs_value' => ['nullable', 'required_unless:ramburs,1', 'numeric', 'min:1', 'max:1000000'],
            'packages.titular_cont' => ['nullable', 'required_if:ramburs,3', 'string', 'min:1', 'max:32'],
            'packages.iban' => ['nullable', 'required_if:ramburs,3', 'string', 'min:1', 'max:50'],
            'packages.customer_reference' => ['nullable', 'string', 'max:50'],
            'packages.assurance' => ['nullable', 'numeric', 'min:0'],
            'packages.voucher' => ['nullable', 'min:1', 'exists:vouchers,code'],
        ];
    }

    public function inputNames() 
    {
        return [
            'sender.country' => __('tara'),
            'sender.name' => __('nume'),
            'sender.phone' => __('telefon'),
            'sender.phone_2' => __('telefon 2'),
            'sender.company' => __('companie'),
            'sender.email' => __('email'),
            'sender.postcode' => __('cod postal'),
            'sender.county' => __('judet'),
            'sender.locality' => __('localitate'),
            'sender.street' => __('strada'),
            'sender.street_nr' => __('nr. strada'),
            'sender.apartment' => __('apartament/nr. casa'),
            'sender.bl_code' => __('bloc'),
            'sender.bl_letter' => __('scara'),
            'sender.intercom' => __('interfon'),
            'sender.floor' => __('etaj'),
            'sender.landmark' => __('reper'),
            'sender.more_information' => __('informatii suplimentare'),   
            'receiver.country' => __('tara'),
            'receiver.name' => __('nume'),
            'receiver.phone' => __('telefon'),
            'receiver.phone_2' => __('telefon 2'),
            'receiver.company' => __('companie'),
            'receiver.email' => __('email'),
            'receiver.postcode' => __('cod postal'),
            'receiver.county' => __('judet'),
            'receiver.locality' => __('localitate'),
            'receiver.street' => __('strada'),
            'receiver.street_nr' => __('nr. strada'),
            'receiver.apartment' => __('apartament/nr. casa'),
            'receiver.bl_code' => __('bloc'),
            'receiver.bl_letter' => __('scara'),
            'receiver.intercom' => __('interfon'),
            'receiver.floor' => __('etaj'),
            'receiver.landmark' => __('reper'),
            'receiver.more_information' => __('informatii suplimentare'),   
            'address_id' => __('adresa'),
            'waiting_approval' => __('verifica comanda'),
            'type' => __('tip colet'),
            'nr_colete' => __('nr. colete'),
            'content' => __('continut'),
            'weight.*' => __('greutate'),
            'length.*' => __('lungime'),
            'width.*' => __('latime'),
            'height.*' => __('inaltime'),
            'volume.*' => __('volum'),
            'awb' => __('AWB'),
            'pickup_day' => __('ziua ridicari'),
            'start_pickup_hour' => __('ora de inceput al ridicari'),
            'end_pickup_hour' => __('ora de sfarsit al ridicari'),
            'options.*' => __('optiune'),
            'ramburs' => __('ramburs'),
            'titular_cont' => __('nume titular cont'),
            'iban' => __('IBAN'),
            'customer_reference' => __('referinta client'),
            'assurance' => __('asigurare'),
            'invoice.country' => __('tara'),
            'invoice.name' => __('nume'),
            'invoice.phone' => __('telefon'),
            'invoice.company' => __('companie'),
            'invoice.email' => __('email'),
            'invoice.is_company' => __('persoana'),
            'invoice.cui_nif' => __('cui/nif'),
            'invoice.nr_reg_com' => __('nr. reg. com.'),
            'invoice.company_name' => __('nume companie'),
            'invoice.postcode' => __('cod postal'),
            'invoice.county' => __('judet'),
            'invoice.locality' => __('localitate'),
            'invoice.street' => __('strada'),
            'invoice.street_nr' => __('nr. strada'),
            'invoice.apartment' => __('apartament/nr. casa'),
            'invoice.bl_code' => __('bloc'),
            'invoice.bl_letter' => __('scara'),
            'invoice.intercom' => __('interfon'),
            'invoice.floor' => __('etaj'),
            'invoice.landmark' => __('reper'),
            'invoice.more_information' => __('informatii suplimentare'),
        ];
    }
}
