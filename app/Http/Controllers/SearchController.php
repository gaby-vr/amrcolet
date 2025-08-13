<?php

namespace App\Http\Controllers;

use App\Courier\CourierGateway;
use App\Models\CodPostal;
use App\Models\Curier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function getCounty(Request $request)
    {
        return json_encode(CodPostal::counties([
            'judet' => $request->input('county').'%'
        ], 5)->select('judet AS denumire')->get());
    }

    public function getLocality(Request $request)
    {
        return json_encode(CodPostal::localities([
            'localitate' => $request->input('search').'%',
            'judet' => $request->input('county').'%'
        ], 5)->groupBy('judet','localitate')->select('localitate AS denumire')->get());
    }

    public function getCountyAndLocality(Request $request)
    {
        return json_encode(CodPostal::localities([
            'localitate' => $request->input('search').'%'
        ], 20)->addSelect('judet')->get());
    }

    public function getStreet(Request $request)
    {
        return json_encode(CodPostal::streets([
            'strada' => $request->input('search').'%',
            'localitate' => $request->input('locality'),
            'judet' => $request->input('county'),
        ], 20)->select('strada','de_la','pana_la','cod_postal')->get());
    }

    public function searchServices(Request $request)
    {
        $input = Validator::make($request->input(), [
            'sender_country' => ['required', 'string', 'max:255'],
            'sender_country_code' => ['required', 'string', 'max:2'],
            'sender_county' => ['required', 'string', 'max:255'],
            'sender_locality' => ['required', 'string', 'max:255'],
            'receiver_country' => ['required', 'string', 'max:255'],
            'receiver_country_code' => ['required', 'string', 'max:2'],
            'receiver_county' => ['required', 'string', 'max:255'],
            'receiver_locality' => ['required', 'string', 'max:255'],
            'receiver_locality' => ['required', 'string', 'max:255'],
            'package_type' => ['required', 'integer', 'min:1', 'max:2'],
            'total_weight' => ['nullable' , 'required_if:package_type,2', 'numeric', 'max:200'],
        ])->validate(); 

        $curieri = Curier::whereNotNull('id');

        if($input['total_weight'] != null) {
            $curieri = Curier::where('max_total_weight', '>=', $input['total_weight']);
        }

        $curieri = $curieri->get();
        
        $curieriTable = view('services.service', ['curieri' => []])->render();

        if(count($curieri) > 0) {
            $prices = [];
            $discounts = [];
            foreach($curieri as $key => $curier) {
                // $value = self::getCurierPrice($curier, $input);
                $total[$curier->id] = isset($total[$curier->id]) ? $total[$curier->id] : self::getCurierPrice($curier, $input);
                $value = $total[$curier->id] != false ? $total[$curier->id] : false;
                if($value) {
                    $prices[$curier->id] = round($value, 2);
                    $package['nr_colete'] = $input['package_type'] == '2' ? 1 : 0;
                    $discounts[$curier->id] = $curier->discount($package['nr_colete']);
                } else {
                    $curieri->forget($key);
                }
            }
            $curieriTable = view('services.service', ['curieri' => $curieri, 'prices' => $prices, 'discounts' => $discounts])->render();
        }
        return view('services.table', ['curieriTable' => $curieriTable])->render();
    }

    public function getCurierPrice(Curier $curier, $attributes)
    {
        $total = [
            'total_price' => false,
            'api_price' => false,
        ];
        $addedProcent = $curier->percent_price;
        switch ($curier->api_curier) {
            case 1: // Urgent Cargus
                $api = app(CourierGateway::class, ['type' => 1]);
                $fromCountry = $api->findCountry(['isoAlpha2' => $attributes['sender_country_code']]);
                if(!$fromCountry) {
                    return false;
                }
                if($attributes['sender_country_code'] == $attributes['receiver_country_code']) {
                    $toCountry = $fromCountry;
                } else {
                    $toCountry = $api->findCountry(['isoAlpha2' => $attributes['receiver_country_code']]);
                }
                $fromCounty = $api->findCounty(['countryId' => $fromCountry['CountryId'], 'name' => $attributes['sender_county']]);
                $toCounty = $api->findCounty(['countryId' => $toCountry['CountryId'], 'name' => $attributes['receiver_county']]);
                if($fromCounty) {
                    $from = $api->findLocality(['countryId' => $fromCountry['CountryId'], 'countyId' => $fromCounty['CountyId'], 'name' => $attributes['sender_locality']]);
                } else {
                    return false;
                }
                if($toCounty) {
                    $to = $api->findLocality(['countryId' => $toCountry['CountryId'], 'countyId' => $toCounty['CountyId'], 'name' => $attributes['receiver_locality']]);
                } else {
                    return false;
                }

                $price = $api->calculateOrder([
                    'from' => [
                        'localityId' => $from['LocalityId'] ?? 0,
                        'countyName' => $from['ParentName'] ?? $attributes['sender_county'],
                        'localityName' => $from['Name'] ?? $attributes['sender_locality'],
                    ],
                    'to' => [
                        'localityId' => $to['LocalityId'] ?? 0,
                        'countyName' => $to['ParentName'] ?? $attributes['receiver_county'],
                        'localityName' => $to['Name'] ?? $attributes['receiver_locality'],
                    ],
                    'parcels' => $attributes['package_type'] == '2' ? 1 : 0,
                    'envelops' => $attributes['package_type'] == '1' ? 1 : 0,
                    'TotalWeight' => $attributes['total_weight'],
                    'DeclaredValue' => 0,
                    'CashRepayment' => 0,
                    'BankRepayment' => 0,
                    'OtherRepayment' => null,
                    'OpenPackage' => false,
                    'MorningDelivery' => false,
                    'SaturdayDelivery' => false,
                    'PackageContent' => '',
                ]); // return GrandTotal

                $total['api_price'] = ($price != false && isset($price['GrandTotal'])) 
                        ? ($price['GrandTotal'] < 19 ? 19 : $price['GrandTotal']) 
                        : false;
                // $value = $curier->minimPriceForKg($attributes['total_weight'] ?? 1);
                
                // return $price != false && isset($price['GrandTotal']) ? ($value ? $value : $price['GrandTotal']) : false;
            case 2: // DPD
                $api = app(CourierGateway::class, ['type' => 2]);

                // sender
                $senderCountry = $api->findCountry(['isoAlpha2' => $attributes['sender_country_code']]);
                $senderSite = false;
                if($senderCountry && $senderCountry['requireState'] == true) {
                    $senderState = $api->findState(['countryId' => $senderCountry['id'], 'name' => '']);
                }
                if($senderCountry && $senderCountry['siteNomen'] > 0) {
                    $senderSite = $api->findSite(['countryId' => $senderCountry['id'], 'postCode' => null, 'name' => $attributes['sender_locality'], 'region' => $attributes['sender_county']]);
                }
                // receiver
                $receiverCountry = $api->findCountry(['isoAlpha2' => $attributes['receiver_country_code']]);
                $receiverSite = false;
                if($receiverCountry && $receiverCountry['requireState'] == true) {
                    $receiverState = $api->findState(['countryId' => $receiverCountry['id'], 'name' => '']);
                }
                if($receiverCountry && $receiverCountry['siteNomen'] > 0) {
                    $receiverSite = $api->findSite(['countryId' => $receiverCountry['id'], 'postCode' => null, 'name' => $attributes['receiver_locality'], 'region' => $attributes['receiver_county']]);
                }


                $price = $api->calculateOrder([
                    'parcels' => [],
                    'senderPrivatePerson' => '',
                    'senderCountryId' => $senderCountry['id'],
                    //'senderSiteType' => $senderCountry['siteNomen'],
                    //'senderSiteName' => $senderSite['name'] ?? $attributes['sender_locality'],
                    'senderSiteId' => $senderSite['id'] ?? null,
                    'senderPostCode' => null,
                    'receiverPrivatePerson' => '',
                    'receiverCountryId' => $receiverCountry['id'],
                    //'receiverSiteType' => $receiverCountry['siteNomen'],
                    //'receiverSiteName' => $receiverSite['name'] ?? $attributes['receiver_locality'],
                    'receiverSiteId' => $receiverSite['id'] ?? null,
                    'parcelsCount' => 1,
                    'totalWeight' => $attributes['total_weight'],
                    'pickupDate' => date('Y-m-d'),
                    'saturdayDelivery' => false,
                    'ramburs' => '1',
                    'declaredValue' => '',
                    'obpd' => '',
                    'rod' => '',
                    'swap' => '',
                    'contents' => '',
                ]);
                
                $total['api_price'] = ($price != false && isset($price['price'])) ? $price['price']['total'] : false;
                // $value = $curier->minimPriceForKg($attributes['total_weight'] ?? 1);

                // return $price != false && isset($price['price']) ? ($value ? $value : $price['price']['total']) : false;
            case 3: // GLS
                try {
                    $api = app(CourierGateway::class, ['type' => $curier->api_curier]);

                    $price = $api->calculateOrder([
                        'parcels' => [
                            ['weight' => $attributes['total_weight']]
                        ],
                        'ramburs' => $package['ramburs'] ?? null,
                    ]);

                    $total['api_price'] = ($price != false && isset($price['total'])) ? $price['total'] : false;
                
                } catch(\Exception $e) {
                    \Log::info($e);
                }

                break;
            default:
                return $total;
        }

        if($total['api_price']) {

            $value = $curier->minimPriceForKg($attributes['total_weight']);
            $added = isset($package['assurance']) && $package['assurance'] > 0 
                ? $package['assurance'] * $curier->percent_assurance/100
                : 0;
            $total['total_price'] = $value + $added;
            if(
                // auth()->check() && auth()->id() == 1 &&
                isset($package['ramburs']) && $package['ramburs'] == '3'
                && isset($package['ramburs_value']) && $package['ramburs_value'] > 0
            ) {
                $total['total_price'] = $curier->addRambursValue($total['total_price']);
            }
            if(isset($package['options']['open_when_received']) && $package['options']['open_when_received'] > 0) {
                $total['total_price'] += $curier->value_owr;
            }
            if(isset($package['options']['retur_document']) && $package['options']['retur_document'] > 0) {
                $total['total_price'] += $curier->minimPriceForKg($package['swap_details']['total_weight'] ?? 1);
            }
        }

        if(!$total['total_price']) {
            $total['total_price'] = $total['api_price'];
        }

        return $total['total_price'];
    }
}
