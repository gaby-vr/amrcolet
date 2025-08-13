<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Courier\CourierGateway;
use App\Invoicing\InvoiceGateway;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Curier;
use App\Models\Invoice;
use App\Models\InvoiceMeta;
use App\Models\Livrare;
use App\Models\Package;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use App\Traits\OrderCreationTrait;
use App\Traits\OrderInvoiceTrait;
use App\Traits\OrderValidationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use OrderCreationTrait, OrderValidationTrait, OrderInvoiceTrait;

    public function index(Request $request)
    {
        // Prepare available days
        $pickup_days = [];
        $first_working_day = now()->subDays(1)->addWeekdays(1)->format('Y-m-d');
        $today = now()->format('Y-m-d');

        // If first working day is today and didn't passed hour 16
        $condition = $first_working_day == $today && now()->format('H') > '16';
        $start_day = $condition ? 2 : 1;
        $prefix_first_day = $first_working_day == $today && $start_day == 1 ? '(Astazi) ' : '';

        for($i = $start_day ; $i <= ($start_day + 2); $i++) {
            $day = now()->subDays(1)->addWeekdays($i);
            $pickup_days[$day->format('Y-m-d')] = ($i == $start_day)
                ? $prefix_first_day.$day->locale('ro')->isoFormat('dddd DD.MM')
                : $day->locale('ro')->isoFormat('dddd DD.MM');
        }
        // END Prepare available days

        $user = auth()->user();
        $invoice_info = $user ? $user->invoiceInfo() : [];


        return view('new-order.new-order', [
            'user' => $user,
            'pickup_days' => $pickup_days,
            'invoice_info' => $invoice_info,
            'schedule' => $user ? $user->getMetas('schedule_') : [],
            'repayment' => $user ? $user->getMetas('repayment_') : [],
            'expiration_date' => $user ? $user->meta('expiration_date') : '',
            'noInvoiceInfo' => $user && ($user->role == '2' || count($invoice_info) > 0) ? false : true,
            'sender_session' => session()->get('sender'),
            'receiver_session' => session()->get('receiver'),
            'package_session' => session()->get('package'),
            'countriesIso' => Country::pluck('iso')->map(function (?string $name) {
                return strtolower($name);
            }),
        ] + (session()->has('repeat') ? [
            'repeat' => 1,
        ] : []));
    }

    public function freeSession(Request $request)
    {
        session()->pull('to_send_email');
        session()->pull('sender');
        session()->pull('receiver');
        session()->pull('package');
        session()->pull('invoice');
        return redirect()->route('order.index');
    }

    public function getInvoice(Request $request)
    { 
        $user = auth()->user();
        if($user) { 
            $invoice = $user->invoiceInfo();
            if(count($invoice) > 0) {
                return response()->json([
                    'status' => 200,
                    'invoice' => $invoice,
                ]);
            } else {
                return response()->json(['status' => 422]);
            }
        } else {
            return response()->json(['status' => 422]);
        }
    }

    public function getCurieri($step = null, $totalSteps = null) 
    {
        if(session()->has('package') && session()->has('receiver') && session()->has('sender') && $step == ($totalSteps - 1)) 
        {
            $package = session()->get('package');
            $receiver = session()->get('receiver');
            $sender = session()->get('sender');
            $parcels = [];
            $conditions = [];
            $conditions['max_weight'] = 0;
            $conditions['total_volume'] = 0;
            $conditions['total_weight'] = 0;
            
            if( $package['nr_colete'] > 0 && $package['type'] == '1') 
            {
                for($i = 0 ; $i < $package['nr_colete'] ; $i++)
                {
                    $parcels[$i]['id'] = $i + 1;
                    $parcels[$i]['weight'] = $package['weight'][$i];
                    $parcels[$i]['width'] = $package['width'][$i];
                    $parcels[$i]['length'] = $package['length'][$i];
                    $parcels[$i]['height'] = $package['height'][$i];

                    $conditions['max_weight'] = $conditions['max_weight'] < $package['weight'][$i] ? $package['weight'][$i] : $conditions['max_weight'];
                    $conditions['total_volume'] += round(($package['width'][$i] * $package['length'][$i] * $package['height'][$i])/6000, 2);
                    $conditions['total_weight'] += $package['weight'][$i];
                }
                $conditions['calc_weight'] = $conditions['total_volume'] > $conditions['total_weight'] ? $conditions['total_volume'] : $conditions['total_weight'];
            } else {
                $conditions['total_weight'] = 1;
                $conditions['calc_weight'] = 1;
            }

            ini_set('max_execution_time', '60');
            $curieri = Curier::where('max_package_weight', '>=', $conditions['max_weight'])
                    ->where('max_total_weight', '>=', $conditions['total_weight'])
                    ->where('last_pick_up_hour', '>=', $package['start_pickup_hour']);
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

            // if the locations of the sender and receiver
            // are outside the default country (Romania)
            // no courier is returned because you can't create orders
            // if both of the locations are outside the default country
            if($sender['country_code'] !== 'ro' && $receiver['country_code'] !== 'ro') {
                $curieri->whereNull('id');
            } elseif($sender['country_code'] !== 'ro' || $receiver['country_code'] !== 'ro') {
                $curieri->externalOrders($package['ramburs'] > 1);
            }

            if(auth()->check()) {
                $curieriSpeciali = $curieri->clone();
            }

            $curieri = $curieri->get();

            if(auth()->check()) {
                $curieriSpeciali = $curieriSpeciali->withoutGlobalScope('active')
                    ->whereNotIn('id', $curieri->pluck('id')->toArray())
                    ->where('type', 2)->get();
            }

            return view('components.order.service', $this->getPricesPerCurier($curieri, $package, $sender, $receiver, $conditions, $parcels))
                .(isset($curieriSpeciali) && $curieriSpeciali->isNotEmpty() ? view('components.order.service', 
                    $this->getPricesPerCurier($curieriSpeciali, $package, $sender, $receiver, $conditions, $parcels) + [
                        'input' => false,
                        'title' => __('Curieri valabili prin contract')
                    ]
                ) : '');

            // return view('new-order.components.service', 
            //     $this->getPricesPerCurier($curieri, $package, $sender, $receiver, $conditions, $parcels) 
            //         + ($curieriSpeciali->isNotEmpty() ? \Arr::prependKeysWith(
            //             $this->getPricesPerCurier($curieriSpeciali, $package, $sender, $receiver, $conditions, $parcels),
            //             'special_'
            //         ) : [])
            // )->render();
            // $total = [];
            // $prices = [];
            // $discounts = [];
            // foreach($curieri as $key => $curier) 
            // {
            //     // $addedProcent = $curier->percent_price;
            //     $total[$curier->id] = isset($total[$curier->id]) 
            //         ? $total[$curier->id] 
            //         : self::getCurierPrice($curier, $package, $sender, $receiver, $conditions, $parcels);
            //     // $value = $total[$curier->id] != false ? $total[$curier->id]['api_price'] * ((100 + $addedProcent)/100) : false;
            //     $value = $total[$curier->id] != false ? $total[$curier->id]['total_price'] : false;
            //     if($value) {
            //         $prices[$curier->id] = round($value,2);
            //         $discounts[$curier->id] = $curier->discount($package['type'] == '1' ? $package['nr_colete'] : 0);
            //     } else {
            //         $curieri->forget($key);
            //     }
            // }

            // return view('new-order.components.service', ['curieri' => $curieri, 'prices' => $prices, 'discounts' => $discounts])->render();
        }
        return false;
    }

    protected function getPricesPerCurier($curieri, $package, $sender, $receiver, $conditions, $parcels)
    {
        $total = [];
        $prices = [];
        $discounts = [];
        foreach($curieri as $key => $curier) 
        {
            // $addedProcent = $curier->percent_price;
            $total[$curier->id] = isset($total[$curier->id]) 
                ? $total[$curier->id] 
                : $this->getCurierPrice($curier, $package, $sender, $receiver, $conditions, $parcels);
            // $value = $total[$curier->id] != false ? $total[$curier->id]['api_price'] * ((100 + $addedProcent)/100) : false;
            $value = $total[$curier->id] != false ? $total[$curier->id]['total_price'] : false;
            if($value) {
                $prices[$curier->id] = round($value,2);
                $discounts[$curier->id] = $curier->discount($package['type'] == '1' ? $package['nr_colete'] : 0);
            } else {
                $curieri->forget($key);
            }
        }

        return [
            'curieri' => $curieri, 
            'prices' => $prices, 
            'discounts' => $discounts
        ];
    }

    public function getCurierPrice(Curier $curier, $package, $sender, $receiver, $newConditions = null, $newParcels = null ) 
    {
        if(!is_array($package) || !is_array($sender) || !is_array($receiver)) {
            return false;
        }
        if($newConditions == null && $newParcels == null) {
            $parcels = [];
            $conditions = [];
            $conditions['max_weight'] = 0;
            $conditions['total_volume'] = 0;
            $conditions['total_weight'] = 0;

            if( $package['nr_colete'] > 0 && $package['type'] == '1') {
                for($i = 0 ; $i < $package['nr_colete'] ; $i++)
                {
                    $parcels[$i]['id'] = $i + 1;
                    $parcels[$i]['weight'] = $package['weight'][$i];
                    $parcels[$i]['width'] = $package['width'][$i];
                    $parcels[$i]['length'] = $package['length'][$i];
                    $parcels[$i]['height'] = $package['height'][$i];

                    $conditions['max_weight'] = $conditions['max_weight'] < $package['weight'][$i] ? $package['weight'][$i] : $conditions['max_weight'];
                    $conditions['total_volume'] += round(($package['width'][$i] * $package['length'][$i] * $package['height'][$i])/6000, 2);
                    $conditions['total_weight'] += $package['weight'][$i];
                }
                $conditions['calc_weight'] = $conditions['total_volume'] > $conditions['total_weight'] ? $conditions['total_volume'] : $conditions['total_weight'];
            } else {
                $conditions['total_weight'] = 1;
                $conditions['calc_weight'] = 1;
            }
        } else {
            $parcels = $newParcels;
            $conditions = $newConditions;
        }

        $total = [
            'total_price' => false,
            'api_price' => false,
        ];

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

                    // \Log::info($from);
                    // \Log::info($sender['locality']);

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
                        'MorningDelivery' => isset($toCounty['SaturdayDelivery']) ? $toCounty['SaturdayDelivery'] : false,
                        'SaturdayDelivery' => isset($package['options']['work_saturday']) && isset($to['SaturdayDelivery']) ? $to['SaturdayDelivery'] : false,
                        'PackageContent' => $package['content'],
                    ]); // return GrandTotal

                    // $total['api_price'] = false;
                    // $total['api_price'] = 19;
                    // $total['api_price'] = ($price != false && isset($price['GrandTotal'])) ? $price['GrandTotal'] : false;
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
                            ? $api->getServiceCode($receiver['country_code'])
                            : $api->getServiceCode($sender['country_code'], false),
                        'currencyCode' => $receiver['country_code'] != 'ro'
                            ? Country::code($receiver['country_code'] ?? 'ro')->currency_code
                            : Country::code($sender['country_code'] ?? 'ro')->currency_code,
                    ]);

                    // if(auth()->id() == 1) {
                    //     dd($price, Country::code('bg')->currency_code);
                    // }

                    $total['api_price'] = ($price != false && isset($price['price'])) ? $price['price']['total'] : false;
                
                } catch(\Exception $e) {
                    \Log::info($e);
                }

                break;
            case 3: // GLS
                if($package['nr_colete'] > 20) {
                    return false;
                }
                try {
                    $api = app(CourierGateway::class, ['type' => $curier->api_curier]);

                    $price = $api->calculateOrder([
                        'parcels' => $parcels,
                        'ramburs' => $package['ramburs'],
                    ]);

                    $total['api_price'] = ($price != false && isset($price['total'])) ? $price['total'] : false;
                
                } catch(\Exception $e) {
                    \Log::info($e);
                }

                break;
            case 5: // 2Ship
                try {
                    $carrierId = (int) $curier->meta('special_2ship_carrier_id');
                    $api = app(CourierGateway::class, ['type' => $curier->api_curier]);

                    $payload = [
                        'CarrierId' => $carrierId,
                        "Sender" => [
                            "Country" => strtoupper($sender['country_code'] ?? 'RO'),
                            "State" => $sender['county'] ?? '',
                            "City" => $sender['locality'] ?? '',
                            "PostalCode" => $sender['postcode'] ?? '',
                            "Address1" => ($sender['street'] ?? '') . ' ' . ($sender['street_nr'] ?? ''),
                            "CompanyName" => $sender['company'] ?? $sender['name'] ?? '',
                            "IsResidential" => false
                        ],
                        "Recipient" => [
                            "Country" => strtoupper($receiver['country_code'] ?? 'RO'),
                            "State" => $receiver['county'] ?? '',
                            "City" => $receiver['locality'] ?? '',
                            "PostalCode" => $receiver['postcode'] ?? '',
                            "Address1" => ($receiver['street'] ?? '') . ' ' . ($receiver['street_nr'] ?? ''),
                            "CompanyName" => $receiver['company'] ?? $receiver['name'] ?? '',
                            "IsResidential" => true
                        ],
                        "Packages" => array_map(function ($parcel) {
                            return [
                                "Weight" => $parcel['weight'],
                                "Length" => $parcel['length'],
                                "Width" => $parcel['width'],
                                "Height" => $parcel['height'],
                                "WeightType" => "Kilograms",
                                "DimensionType" => "Centimeters",
                                "Packaging" => "Package",
                                // "IsStackable" => false,
                                // "ApplyWeightAndDimsFromTheAssignedCommodity" => false
                            ];
                        }, $parcels),
                        "PickupDate" => $api->formatDate($package['pickup_day'], $package['start_pickup_hour']),
                        // "Billing" => [
                        //     "BillingType" => "Prepaid"
                        // ],
                        "ShipmentProtection" => $package['assurance'] ?? 0,
                        "ShipmentProtectionCurrency" => "RON",
                        "GlobalOptions" => [
                            "SaturdayDelivery" => $package['options']['work_saturday'] ?? false
                        ]
                    ];
            
                    $price = $api->calculateOrder(['payload' => $payload, 'carrierId' => $carrierId]);
                    $total['api_price'] = $price ?? false;
            
                } catch(\Exception $e) {
                    \Log::error('2Ship error: ' . $e->getMessage());
                }
            
                break;
            default:
                return $total;
        }

        // $country_code = $receiver['country_code'] !== 'ro' ? $receiver['country_code'] : ($sender['country_code'] !== 'ro' ? $sender['country_code'] : null);

        if($total['api_price']) {

            // $value = $curier->minimPriceForKg($conditions['calc_weight']);
            // $added = isset($package['assurance']) && $package['assurance'] > 0 
            //     ? $package['assurance'] * $curier->percent_assurance/100
            //     : 0;
            // $total['total_price'] = $value + $added;
            // if(
            //     // auth()->check() && auth()->id() == 1 &&
            //     isset($package['ramburs']) && $package['ramburs'] == '3'
            //     && isset($package['ramburs_value']) && $package['ramburs_value'] > 0
            // ) {
            //     $total['total_price'] = $curier->addRambursValue($total['total_price']);
            // }
            // if(isset($package['options']['open_when_received']) && $package['options']['open_when_received'] > 0) {
            //     $total['total_price'] += $curier->value_owr;
            // }
            // if(isset($package['options']['retur_document']) && $package['options']['retur_document'] > 0) {
            //     $total['total_price'] += $curier->minimPriceForKg($package['swap_details']['total_weight'] ?? 1);
            // }
            $receiver['country_code'] = $receiver['country_code'] ?? 'ro';
            $sender['country_code'] = $sender['country_code'] ?? 'ro';

            $total['total_price'] = $curier->calculatePriceForConditions(
                $package, $conditions['calc_weight'], null, $parcels, null,
                $receiver['country_code'] !== 'ro' ? $receiver['country_code'] : ($sender['country_code'] !== 'ro' ? $sender['country_code'] : null)
            );
        }

        if(!$total['total_price']) {
            return false;
            // $total['total_price'] = $total['api_price'];
        }

        return $total;
    }

    public function stepEmail(Request $request)
    {
        Validator::make($request->input(), [
            'to_send_email' => ['required', 'string', 'email', 'max:255'],
        ])->validate();
        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()->messages(), 
                'step' => 'to_send_email', 
                'status' => 422
            ]);
        }

        session()->put('to_send_email', $validated->validate());
        return response()->json(['to_send_email' => $request->input('to_send_email'), 'status' => 200]);
    }

    public function stepExpeditor(Request $request)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $validated = Validator::make($request->input(), $this->addressRules('sender'), [], $this->addressNames('sender'));
        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()->messages(), 
                'step' => 'sender', 
                'status' => 422
            ]);
        }

        session()->put('sender', $validated->validate()['sender']);

        return response()->json([
            'array' => $request->input('sender'), 
            'curieri' => self::getCurieri($request->index, $request->total_steps), 
            'step' => 'sender', 
            'status' => 200
        ]);
    }

    public function stepReceiver(Request $request)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $validated = Validator::make($request->input(), $this->addressRules('receiver'), [], $this->addressNames('receiver'));
        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()->messages(), 
                'step' => 'receiver', 
                'status' => 422
            ]);
        }

        session()->put('receiver', $validated->validate()['receiver']);

        return response()->json([
            'array' => $request->input('receiver'), 
            'curieri' => self::getCurieri($request->index, $request->total_steps), 
            'step' => 'receiver', 
            'status' => 200
        ]);
    }

    public function stepPackage(Request $request)
    {
        $validated = Validator::make($request->input(),  $this->packageRules(), [], $this->packageNames());
        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()->messages(), 
                'step' => 'package', 
                'status' => 422
            ]);
        }

        $package = $validated->validate();
        if($package['type'] == 2) {
            $package['nr_colete'] = 0;
        }

        if(isset($package['voucher']) && $package['voucher'] != null) {
            $voucher = Voucher::firstWhere('code', $package['voucher']);

            $package['voucher_code'] = $voucher->code;
            $package['voucher_type'] = $voucher->type;
            $package['voucher_value'] = $voucher->value;
        }
        session()->put('package', $package);

        return response()->json([
            'array' => $package, 
            'curieri' => self::getCurieri($request->index, $request->total_steps), 
            'step' => 'package', 
            'status' => 200
        ]);
    }

    public function stepInvoice(Request $request)
    {
        $request = $this->trimPhoneNumberSpaces($request);
        $validated = Validator::make($request->input(), $this->addressRules('invoice'), [], $this->addressNames('invoice'));
        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()->messages(), 
                'step' => 'invoice', 
                'status' => 422
            ]);
        }

        session()->put('invoice', $validated->validate()['invoice']);

        return response()->json([
            'array' => $request->input('invoice'), 
            'curieri' => self::getCurieri($request->index, $request->total_steps), 
            'step' => 'invoice', 
            'status' => 200
        ]);
    }

    public function stepService(Request $request)
    {
        $validated = Validator::make($request->input(), $this->serviceRules());

        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()->messages(), 
                'step' => 'invoice', 
                'status' => 422
            ]);
        }
    }

    public function storePackage(Request $request)
    {
        $user = auth()->user();
        $guest = $user ? false : true;
        $invoiceInfo = $user ? $user->invoiceInfo() : [];
        $noInvoiceInfo = count($invoiceInfo) > 0 ? false : true;
        $expire_date = $user ? $user->meta('expiration_date') : '';

        if($user && $user->role == '2' && $expire_date != '' && $expire_date < date('Y-m-d')) {
            session()->flash('expiration_date_passed', __('Perioada de comanda fara plata s-a terminat.<br>Plateste comenzile neachitate pentru a continua.'));
            return redirect()->route('home');
        }

        // Trim phone number
        $request = $this->trimPhoneNumberSpaces($request);
        // Validation 
        $attributes = $request->validate($this->allRules($noInvoiceInfo, $guest),[], $this->allNames());

        if($guest && !session()->has('to_send_email')) {
            return back()->withErrors([__('Nu exista un email de trimitere al facturi.')])->withInput();
        }
        // END Validation

        $package = [];
        $sender = $attributes['sender'];
        unset($attributes['sender']);
        $receiver = $attributes['receiver'];
        unset($attributes['receiver']);
        $invoice_form_info = isset($attributes['invoice']) ? $attributes['invoice'] : null;
        unset($attributes['invoice']);
        $livrare = $attributes;

        // Add invoice info
        $contactInvoice = $noInvoiceInfo ? $invoice_form_info : $invoiceInfo;
        $contactInvoice = [
            'phone' => isset($contactInvoice['phone_full']) 
                ? $contactInvoice['phone_full'] 
                : $contactInvoice['phone'],
        ] + (isset($contactInvoice['nr_reg']) ? [
            'nr_reg_com' => $contactInvoice['nr_reg']
        ] : []) + $contactInvoice;

        $curier = Curier::firstWhere('name', $request->input('curier'));
        $pickup_day = Carbon::parse($livrare['pickup_day'] ?? now()->format('Y-m-d'));
        // Add courier and check pickup day
        $livrare = [
            'nr_colete' => $livrare['type'] == '1' ? $livrare['nr_colete'] : 0,
            'curier' => $curier->name,
            'api' => $curier->api_curier,
            'pickup_day' => (time() >= strtotime($curier->last_order_hour.":00:00") 
                && $pickup_day->format('Y-m-d') == now()->format('Y-m-d'))
                    ? $pickup_day->addDays(1)
                    : $pickup_day,

        ] + $livrare;

        if($receiver['country_code'] != 'ro' || $sender['country_code'] != 'ro') {
            $livrare['ramburs_currency'] = $receiver['country_code'] != 'ro'
                ? Country::code($receiver['country_code'] ?? 'ro')->currency_code
                : Country::code($sender['country_code'] ?? 'ro')->currency_code;
        }

        // Calculate price
        $normalPrice = self::getCurierPrice($curier, $livrare, $sender, $receiver);

        // $normalPrice = self::getCurierPrice($curier, $livrare, $sender, $receiver)['total_price'];

        if(isset($normalPrice['total_price'])) {
            $normalPrice = $normalPrice['total_price'];
        } else {
            return back()->withErrors([
                __('Eroare la calcularea pretului, incarcati din nou sau contactati un admin.')
            ])->withInput();
        }
        if(isset($livrare['nr_colete']) && $livrare['nr_colete'] > 0) {
            $price = $normalPrice - $normalPrice * ($curier->discount($livrare['nr_colete'])/100);
        } else {
            // in case that is an envelope it always count as 1 package
            $price = $normalPrice - $normalPrice * ($curier->discount(1)/100);
        }
        $price = round($price, 2);

        // Add packages
        if($livrare['type'] == '1' && $livrare['nr_colete'] > 0) {
            $livrare['total_volume'] = 0;
            $livrare['total_weight'] = 0;
            for($i = 0 ; $i < $livrare['nr_colete'] ; $i++)
            {
                $package[$i] = [
                    'weight' => $livrare['weight'][$i],

                    'width' => $livrare['width'][$i],
                    'length' => $livrare['length'][$i],
                    'height' => $livrare['height'][$i],
                    'volume' => round(($livrare['width'][$i] * $livrare['length'][$i] * $livrare['height'][$i])/6000, 2)
                ];

                $livrare['total_volume'] += $package[$i]['volume'];
                $livrare['total_weight'] += $livrare['weight'][$i];
            }
        }

        $livrare = [
            'original_value' => $price,
        ] + (!isset($livrare['assurance']) || $livrare['assurance'] == null ? [
            'assurance' => 0,
        ] : []) + $livrare;

        // Add voucher if exists
        if(isset($livrare['voucher'])) {
            $voucher = Voucher::firstWhere('code', $livrare['voucher']);

            $livrare = [
                'value' => $voucher->type == '1' 
                    ? ($livrare['original_value'] - $voucher->value)
                    : round(($livrare['original_value'] - ($livrare['original_value'] * $voucher->value/100)), 2),
                'voucher_code' => $voucher->code,
                'voucher_type' => $voucher->type,
                'voucher_value' => $voucher->value
            ] + $livrare;
        } else {
            $livrare['value'] = $livrare['original_value'];
        }

        // Add options if exists
        if(isset($livrare['options'])) {
            foreach($livrare['options'] as $key => $value) {
                $livrare[$key] = $value;
            }
        }

        $amount = $livrare['value'];
        
        // Check if user is logged in
        if(!$guest) {
            $livrare['email'] = $user->email;
            $livrare['user_id'] = $user->id;
            $livrare = Livrare::create($livrare);
                
            $account_balance = $user->meta('account_balance');
            if($account_balance >= $livrare->value) {
                if($account_balance > $livrare->value) {
                    $user->setMeta('account_balance', $account_balance - $livrare->value);
                } else {
                    $user->unsetMeta('account_balance');
                }
                $livrare->nr_credits_used = $livrare->value;
                $livrare->save();
                $amount = 0;
            } else {
                // Check if the user is a contractant (a user who has a contract with the admin(owner))
                if($user->role == '2') {
                    if($account_balance != '' && $account_balance > 0) {
                        $livrare->nr_credits_used = $account_balance;
                        $user->setMeta('account_balance', $account_balance - $livrare->value);
                        $user->unsetMeta('expiration_date');
                    } elseif($account_balance != '') {
                        $user->setMeta('account_balance', $account_balance - $livrare->value);
                    } else {
                        $user->setMeta('account_balance', -1 * $livrare->value);
                    }
                    $livrare->payed = 0;
                    $livrare->save();
                    if($expire_date == '') {
                        $user->setMeta('expiration_date', now()->addDays($user->meta('days_of_negative_balance'))->format('Y-m-d'));
                    }
                    $amount = 0;
                } else {
                    if($account_balance != '' && $account_balance > 0) {
                        $amount -= $account_balance;
                        $livrare->nr_credits_used = $account_balance;
                        $livrare->save();
                        $user->unsetMeta('account_balance');
                    }
                }
            }
        } else {
            $livrare['email'] = $attributes['to_send_email'];
            $livrare['user_id'] = null;
            $livrare = Livrare::create($livrare); 
        }

        // Store sender and receiver
        foreach ([1, 2] as $contact_type) {
            $contact = $contact_type == 1 ? $sender : $receiver;
            $contact['type'] = $contact_type;
            $contact['livrare_id'] = $livrare->id;
            $contact['phone'] = $contact['phone_full'];
            if($contact['phone'][0] == '7') {
                $contact['phone'] = '+40'.$contact['phone'];
            }
            if(isset($contact['phone_2'])) {
                $contact['phone_2'] = $contact['phone_2_full'] ?? $contact['phone_2'];
                if($contact['phone_2'][0] == '7') {
                    $contact['phone_2'] = '+40'.$contact['phone_2'];
                }
            }
            $contact = Contact::create($contact);
            if($contact_type) {
                $sender = $contact;
            } else {
                $receiver = $contact;
            }
        }

        // Create packages
        if($livrare->type == '1') {
            for($i = 0 ; $i < $livrare->nr_colete ; $i++) {
                Package::create($package[$i] + [
                    'type' => 1,
                    'livrare_id' => $livrare->id,
                    'template_id' => 0
                ]);
            }
        }

        if($amount <= 0) {
            self::createOrder($livrare);
            self::sendNotification($livrare->id);
            session()->put('status_order', 1);
            return redirect()->route('order.return', $livrare->id);
        }

        // Create invoice if necesarry
        $invoice = Invoice::create([
            'user_id' => $user ? $user->id : null,
            'status' => 0,
            'total' => $livrare->value,
        ]);

        // Create user address
        $address = 'Str. '.$contactInvoice['street'].
        (isset($contactInvoice['street_nr']) ? ' Nr. '.$contactInvoice['street_nr'] : '').
        (isset($contactInvoice['bl_code']) ? ', Bl. '.$contactInvoice['bl_code'] : '').
        (isset($contactInvoice['bl_letter']) ? ', Sc. '.$contactInvoice['bl_letter'] : '').
        (isset($contactInvoice['intercom']) ? ', Interfon '.$contactInvoice['intercom'] : '').
        (isset($contactInvoice['floor']) ? ', Etaj '.$contactInvoice['floor'] : '').
        (isset($contactInvoice['apartment']) ? ', Ap./Nr. '.$contactInvoice['apartment'] : '');

        // Add user info to invoice
        $metas = [];
        foreach($contactInvoice as $key => $value) {
            if($key == 'company_name') {
                $value != null ? ($metas['nume_firma'] = $value) : null;
            } else {
                $value != null ? ($metas[$key] = $value) : null;
            }
        }
        $metas = [
            'email' => $livrare->email,
            'address' => $address,
            'type' => isset($contactInvoice['is_company']) ? 2 : 1,
        ] + $metas;
        $invoice->setMetas($metas, 'client_');

        $data['form'] = null;

        $paymentGateway = app(PaymentGateway::class, [
            'returnURL'     => route('order.return', $livrare->id),
            'confirmURL'    => route('order.confirm'),
            'amount'        => $amount,
            'firstName'     => $contactInvoice['first_name'],
            'lastName'      => $contactInvoice['last_name'],
            'email'         => $livrare->email,
            'address'       => $address,
            'phone'         => $contactInvoice['phone'],
            'params'        => array( 'invoice_id' => $invoice->id, 'livrare_id' => $livrare->id ),
            'type'          => isset($contactInvoice['is_company']) ? 'company' : 'person', // 'company',
        ]);
        $data['form'] = $paymentGateway->setForm();

        return view('home', [
            'dataOrder' => $data,
        ]);
    }

    public function return(Request $request, $livrare = null)
    {
        session()->pull('to_send_email');
        session()->pull('sender');
        session()->pull('receiver');
        session()->pull('package');
        session()->pull('invoice');
        session()->flash('orderConfirmed', true);

        $livrare = Livrare::firstWhere('id', $livrare);
        $status = session()->pull('status_order');
        return view('page', [
            'page' => Page::firstWhere('slug', 'home'),
            'orderId' => (request()->input('orderId') ?? '1'),
            'livrare' => $livrare,
            'status' => $livrare && $livrare->invoice ? $livrare->invoice->status : $status ?? null,
        ]);
    }

    public function confirm()
    {
        $paymentGateway = app(PaymentGateway::class/*, ['sandbox' => true]*/);
        $data = $paymentGateway->confirm();
        
        if(isset($data['status']) && $data['status'] == 'confirmed')
        {
            ///////////////////////////////////////////////
            // if($livrare = Livrare::find($data['params']['livrare_id'])) {
            //     if($livrare->user_id == '1') {
            //         $invoice = $this->confirmInvoiceApi($data['params']['invoice_id'], [$data['params']['livrare_id']], $data['orderId']);
            //         if($invoice && $livrare = $invoice->livrare) {
            //             $this->createOrder($livrare);
            //             $this->sendNotification($data['params']['livrare_id']);
            //         }
            //         return true;
            //     }
            // }
            ///////////////////////////////////////////////
            // $livrare = self::confirmInvoice($data['params']['invoice_id'], $data['params']['livrare_id'], $data['orderId']);
            // if($livrare) {
            //     self::createOrder($livrare);
            //     self::sendNotification($data['params']['livrare_id']);
            // }
            $invoice = $this->confirmInvoiceApi($data['params']['invoice_id'], [$data['params']['livrare_id']], $data['orderId']);
            if($invoice && $livrare = $invoice->livrare) {
                $this->createOrder($livrare);
                $this->sendNotification($data['params']['livrare_id']);
            }
        }
        elseif(isset($data['status']) && $data['status'] == 'canceled')
        {
            Invoice::where('id', $data['params']['invoice_id'])->update([
                'status' => 2, // Anulata
            ]);
            InvoiceMeta::where('invoice_id', $data['params']['invoice_id'])->delete();
            if($livrare = Livrare::where('id', $data['params']['livrare_id'])->first()) {
                if($livrare->status != 5) {
                    $this->returnCredits($livrare);
                }
            }
            Livrare::where('id', $data['params']['livrare_id'])->update(['status' => 5]);
        }
        elseif(isset($data['status']) && $data['status'] == 'credit')
        {
            Invoice::where('id', $data['params']['invoice_id'])->update([
                'status' => 3, // Stornata
            ]);

            if($livrare = Livrare::where('id', $data['params']['livrare_id'] ?? null)->first()) {
                if($livrare->status != 5) {
                    $this->returnCredits($livrare);
                }
            }
        }
        elseif(isset($data['status']) && $data['status'] == 'rejected')
        {
            Invoice::where('id', $data['params']['invoice_id'])->update([
                'status' => 4, // Respinsa
            ]);
            if($livrare = Livrare::where('id', $data['params']['livrare_id'])->first()) {
                if($livrare->status != 5) {
                    $this->returnCredits($livrare);
                }
            }
            Livrare::where('id', $data['params']['livrare_id'])->update(['status' => 5]);

            // Invoice::where('id', $data['params']['invoice_id'])->delete();
            // InvoiceMeta::where('invoice_id', $data['params']['invoice_id'])->delete();
            // Livrare::where('id', $data['params']['livrare_id'])->delete();
        }
    }

    protected function returnCredits(Livrare $livrare)
    {
        if($livrare->nr_credits_used && $user = $livrare->user) {
            $account_balance = $user->meta('account_balance');
            if($account_balance != '') {
                $user->setMeta('account_balance', (float)$account_balance + (float)$livrare->nr_credits_used);
            } else {
                $user->setMeta('account_balance', $livrare->nr_credits_used);
            } 
        }
    }

    public function sendNotification($livrare_id)
    {
        $livrare = Livrare::firstWhere('id', $livrare_id);
        if(($livrare->user && $livrare->user->meta('notifications_invoice_active') == '1' || $livrare->user_id == '0'|| $livrare->user_id == null) && $livrare->status != 5) {
            $sender = $livrare->sender;
            $receiver = $livrare->receiver;
            if($livrare->type == '2') {
                $order = 'Detalii expediere: Plic<br>';
            } else {
                $order = 'Detalii expediere: Nr. de colete '.$livrare->nr_colete.' cu urmatoarele specificatii: <br><ul>';
                $packages = Package::where('livrare_id', $livrare->id)->get();
                foreach($packages as $package) {
                    $order .= '<li>Greutate: '.$package->weight.' kg, Lungime: '.$package->length.' cm, Latime: '.$package->width.' cm. Inaltime: '.$package->height.' cm, greutate volumetrica: '.$package->volume.' kg</li>';
                }
                $order .= '</ul><br>';
            }
            $ramburs = $livrare->ramburs > 1 ? ($livrare->ramburs > 2 ? 'Ramburs in cont:' : 'Ramburs cash ').' '.$livrare->ramburs_value.' ron<br>' : '';
            $details['action'] = 1;
            $details['send_awb'] = $livrare->user && $livrare->user->meta('notifications_awb_active') != '1' ? false : true;
            $details['awb_api'] = $livrare->api;
            $details['awb_shipment_id'] = $livrare->api_shipment_awb ?? $livrare->api_shipment_id;
            $details['total_weight'] = $livrare->total_weight;
            $details['created_at'] = $livrare->created_at;
            $details['invoice_id'] = $livrare->invoice ? $livrare->invoice_id : null;
            $details['curier_name'] = $livrare->curier;
            $details['subject'] = __('Expediere noua catre :name', ['name' => $receiver->name]);
            $details['title'] = '';
            $details['body'] = __('Stimate client,<br><br>');
            $details['body'] .= __('A fost creat urmatorul AWB pentru transport: <br>');
            $details['body'] .= __('Nume curier: :curier<br>', ['curier' => $livrare->curier]);
            $details['body'] .= __('AWB: :awb<br>', ['awb' => $livrare->api_shipment_awb]);
            $details['body'] .= __('Expeditor:  :sender_name - :sender_locality <br>', ['sender_name' => $sender->name, 'sender_locality' => $sender->locality]);
            $details['body'] .= __('Destinatar: :receiver_name - :receiver_locality <br>', ['receiver_name' => $receiver->name, 'receiver_locality' => $receiver->locality]);
            $details['body'] .= $order;
            $details['body'] .= $ramburs;
            $details['body'] .= __('<b style="color:red">Va rugam sa tineti cont de faptul ca in cazul in care greutatea sau greutatea volumetrica este mai mare decat cea declarata, curierul poate refuza ridicarea acestuia sau, daca acesta este ridicat, va fi cantarit si masurat in depozit, iar diferenta va fi facturata.</b><br><br>');
            $details['body'] .= __('Puteti urmari status-ul comenzii accesand pagina <a href=":url_comanda">:url_comanda</a><br><br>', ['url_comanda' => route('dashboard.orders.show')]);
            $details['body'] .= __('Va dorim o zi placuta,<br>');
            $details['body'] .= __('Echipa <a href=":url_home">amrcolet.ro</a>', ['url_home' => route('home')]);
            try {
                Mail::to($livrare->email)->send(new \App\Mail\SendPurchaseNotification($details));
            } catch(\Exception $e) { \Log::info($e); }
        }
    }

    public function confirmInvoice($invoice_id, $livrare_id, $order_id = null) 
    {
        $invoice = Invoice::firstWhere('id', $invoice_id);
        if($invoice && $invoice->status != 1) 
        {
            $setari = setari([
                'INVOICE_',
                'PROVIDER_'
            ], true, false, true);

            Invoice::where('id', $invoice_id)->update([
                'status' => 1,
                'series' => $setari['INVOICE_SERIES'],
                'number' => $setari['INVOICE_NR'],
                'payed_on' => now(),
            ]);

            Setting::firstWhere('name', 'INVOICE_NR')->increment('value');

            $livrare = Livrare::firstWhere('id', $livrare_id);

            $metas = [];
            if($order_id != null) {
                $metas['mobilpay_order_id'] = $order_id;
            }

            $metas = [
                // set total value
                'product_total_price' => $livrare->value,

                // add provider info
                'provider_name' => $setari['PROVIDER_NAME'],
                'provider_email' => $setari['PROVIDER_EMAIL'],
                'provider_phone' => $setari['PROVIDER_PHONE'],
                'provider_address' => $setari['PROVIDER_ADDRESS'],
                'provider_nr_reg' => $setari['PROVIDER_NR_REG'],
                'provider_iban' => $setari['PROVIDER_IBAN'],
                'provider_cui' => $setari['PROVIDER_CUI'],
                'provider_cap_social' => $setari['PROVIDER_CAP_SOCIAL'],

                // add product info
                'product_nr_products' => '1',
                'product_name_0' => 'Comanda #'.$livrare->id,
                'product_qty_0' => '1',
                'product_price_0' => $livrare->original_value,
            ] + $metas;

            if($livrare->type == '1') {
                $packages = Package::where('livrare_id', $livrare->id)->get();
                $i = 0;
                $invoiceDescription = '';
                foreach($packages as $package) {
                    if($i > 0) {
                        $invoiceDescription .= "\r\n";
                    }
                    $invoiceDescription .= 'Colet '.($i + 1).' - '.$package->weight.'kg, '.$package->width.' x '.$package->length.' x '.$package->height;
                    $i++;
                }
                $metas['product_description_0'] = $invoiceDescription;
            } else {
                $metas['product_description_0'] = 'Plic';
            }

            // add voucher if necesary
            if($livrare->voucher_code != null) {
                $value = $livrare->voucher_type == '1' 
                    ? $livrare->voucher_value 
                    : round($livrare->original_value * ($livrare->voucher_value/100), 2);

                $metas = [
                    'product_nr_products' => '2',
                    'product_name_1' => 'Voucher "'.$livrare->voucher_code.'" '.$livrare->voucher_value.($livrare->voucher_type == '1' ? ' RON' : '%'),
                    'product_qty_1' => '1',
                    'product_price_1' => -1 * $value,
                    'product_description_1' => $livrare->voucher_type == '1' ? 'Valoare' : 'Procent',
                ] + $metas;
            }

            if($livrare->nr_credits_used > 0) {
                $metas = [
                    'product_nr_products' => '3',
                    'product_name_2' => __('Credite extrase din cont'),
                    'product_qty_2' => $livrare->nr_credits_used,
                    'product_price_2' => -1,
                ] + $metas;

                $invoice->total -= $livrare->nr_credits_used;
                $invoice->save();
            }
            $invoice->setMetas($metas);

            $livrare->invoice_id = $invoice->id;
            $livrare->save();
            return $livrare;
        }
        return false;
    }
}
