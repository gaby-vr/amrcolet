<?php

namespace App\Traits;

use App\Courier\CourierGateway;
use App\Models\Country;
use App\Models\Curier;
use App\Models\Invoice;
use App\Models\Livrare;
use App\Models\Package;
use App\Models\Repayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait OrderCreationTrait
{
    public function createOrder(Livrare $livrare)
    {
        $parcels = [];
        if($livrare->type == '1')
        {
            $packages = Package::where('livrare_id', $livrare->id)->get();
            foreach($packages as $index => $package) {
                $parcels[$index]['id'] = $index + 1;
                $parcels[$index]['width'] = $package->width;
                $parcels[$index]['length'] = $package->length;
                $parcels[$index]['height'] = $package->height;
                $parcels[$index]['weight'] = $package->weight;
            }
        }
        $curier = Curier::firstWhere('name', $livrare->curier);
        switch ($livrare->api) {
            case 1:
                self::createUrgentCargusOrder($livrare, $curier, $parcels);
                break;
            case 2:
                self::createDPDOrder($livrare, $curier, $parcels);
                break;
            case 3:
                self::createGLSOrder($livrare, $curier, $parcels);
                break;
            case 5: // 2Ship
                self::createTwoShipOrder($livrare, $curier, $parcels);
                break;
            default:
                break;
        }
    }

    public function createUrgentCargusOrder(Livrare $livrare, $curier, $parcels) 
    {
        $api = app(CourierGateway::class, ['type' => 1]);

        $sender = $livrare->sender;
        $receiver = $livrare->receiver;

        $fromCountry = $api->findCountry(['isoAlpha2' => $sender['country_code']]);
        if($sender['country_code'] == $receiver['country_code']) {
            $toCountry = $fromCountry;
        } else {
            $toCountry = $api->findCountry(['isoAlpha2' => $receiver['country_code']]);
        }
        $fromCounty = $api->findCounty(['countryId' => $fromCountry['CountryId'], 'name' => $sender['county']]);
        $toCounty = $api->findCounty(['countryId' => $toCountry['CountryId'], 'name' => $receiver['county']]);
        $from = $api->findLocality(['countryId' => $fromCountry['CountryId'], 'countyId' => $fromCounty['CountyId'], 'name' => $sender['locality']]);
        $to = $api->findLocality(['countryId' => $toCountry['CountryId'], 'countyId' => $toCounty['CountyId'], 'name' => $receiver['locality']]);
        
        if($from && isset($from['LocalityId'])) {
            $fromStreet = $api->findStreet(['localityId' => $from['LocalityId'], 'postcode' => $sender['postcode']]);
        }
        if($to && isset($to['LocalityId'])) {
            $toStreet = $api->findStreet(['localityId' => $to['LocalityId'], 'postcode' => $receiver['postcode']]);
        }

        $senderAddress =
            (isset($sender['street']) && $sender['street'] != '-' ? 'Strada '.$sender['street'] : '').
            (isset($sender['street_nr']) && $sender['street_nr'] != '-' ? ' Nr. '.$sender['street_nr'] : '').
            (isset($sender['bl_code']) ? 'Bl. '.$sender['bl_code'] : '').
            (isset($sender['bl_letter']) ? ', Sc. '.$sender['bl_letter'] : '').
            (isset($sender['intercom']) ? ', Interfon '.$sender['intercom'] : '').
            (isset($sender['floor']) ? ', Etaj '.$sender['floor'] : '').
            (isset($sender['apartment']) ? ', Ap./Nr. '.$sender['apartment'] : '');
        $receiverAddress =
            (isset($receiver['street']) && $receiver['street'] != '-' ? 'Strada '.$receiver['street'] : '').
            (isset($receiver['street_nr']) && $receiver['street_nr'] != '-' ? ' Nr. '.$receiver['street_nr'] : '').
            (isset($receiver['bl_code']) ? 'Bl. '.$receiver['bl_code'] : '').
            (isset($receiver['bl_letter']) ? ', Sc. '.$receiver['bl_letter'] : '').
            (isset($receiver['intercom']) ? ', Interfon '.$receiver['intercom'] : '').
            (isset($receiver['floor']) ? ', Etaj '.$receiver['floor'] : '').
            (isset($receiver['apartment']) ? ', Ap./Nr. '.$receiver['apartment'] : '');

        // check if postal code has multiple street
        // to prevent showing 2 streets on the awb
        // $sender['postcode'] = CodPostal::onlyPostalCode($sender['postcode']) ? $sender['postcode'] : null;
        // $receiver['postcode'] = CodPostal::onlyPostalCode($receiver['postcode']) ? $receiver['postcode'] : null;

        $response = $api->setOrder([
            'PickupStartDate' => $livrare['pickup_day'] instanceof Carbon 
                ? $livrare['pickup_day']->format('Y-m-d') 
                : $livrare['pickup_day'],
            'PickupEndDate' => $livrare['pickup_day'] instanceof Carbon 
                ? $livrare['pickup_day']->format('Y-m-d') 
                : $livrare['pickup_day'],
            'PickupStartHour' => $livrare['start_pickup_hour'],
            'PickupEndHour' => $livrare['end_pickup_hour'],
            'sender' => [
                'name' => $sender['company'] ?? $sender['name'],
                'countyId' => $fromCounty['CountyId'] ?? 0,
                'countyName' => $fromCounty['Name'] ?? $sender['county'],
                'localityId' => $from['LocalityId'] ?? 0,
                'localityName' => $from['Name'] ?? $sender['locality'],
                'streetId' => $fromStreet['StreetId'] ?? 0,
                'streetName' => $sender['street'] ?? $fromStreet['Name'] ?? null,
                'buildingNumber' => $sender['street_nr'] ?? 0,
                'address' => $senderAddress,
                'contactPerson' => $sender['name'],
                'phone' => $sender['phone'],
                'email' => $sender['email'],
                'postcode' => $sender['postcode'],
                'countryId' => $fromCountry['CountryId']
            ],
            'receiver' => [
                'name' => $receiver['company'] ?? $receiver['name'],
                'countyId' => $toCounty['CountyId'] ?? 0,
                'countyName' => $toCounty['Name'] ?? $receiver['county'],
                'localityId' => $to['LocalityId'] ?? 0,
                'localityName' => $to['Name'] ?? $receiver['locality'],
                'streetId' => $toStreet['StreetId'] ?? 0,
                'streetName' => $receiver['street'] ?? $toStreet['Name'] ?? null,
                'buildingNumber' => $receiver['street_nr'] ?? 0,
                'address' => $receiverAddress,
                'contactPerson' => $receiver['name'],
                'phone' => $receiver['phone'],
                'email' => $receiver['email'],
                'postcode' => $receiver['postcode'],
                'countryId' => $toCountry['CountryId']
            ],
            'parcels' => $livrare['nr_colete'] ?? 0,
            'envelops' => $livrare['nr_colete'] > 0 ? 0 : 1,
            'ParcelCodes' => $parcels,
            'TotalWeight' => $livrare['total_weight'] ?? 1,
            'DeclaredValue' => $livrare['assurance'] ?? '',
            'CashRepayment' => $livrare['ramburs'] == '2' ? ($livrare['ramburs_value'] ?? 0) : 0,
            'BankRepayment' => $livrare['ramburs'] == '3' ? ($livrare['ramburs_value'] ?? 0) : 0,
            'OtherRepayment' => null,
            'OpenPackage' => $livrare['open_when_received'] != null ? true : false,
            'MorningDelivery' => isset($toCounty['MorningDelivery']) ? $toCounty['MorningDelivery'] : false,
            'SaturdayDelivery' => $livrare['work_saturday'] != null && isset($to['SaturdayDelivery']) ? $to['SaturdayDelivery'] : false,
            'PackageContent' => $livrare['content'],
        ]);

        if(isset($response['IdComanda'])) {
            $livrare->api_shipment_id = $response['IdComanda'];
            $livrare->api_shipment_awb = $response['BarCode'];
        } else {
            \Log::info($response);
        }
        $livrare->save();

        $receiver['full_address'] = $receiverAddress;
        $this->creareRepayment($livrare, $receiver);
    }

    public function createDPDOrder(Livrare $livrare, $curier, $parcels) 
    {
        $api = app(CourierGateway::class, ['type' => 2]);

        $sender = $livrare->sender;
        $receiver = $livrare->receiver;

        if($livrare['nr_colete'] > 10) {
            return false;
        } elseif(($sender['country_code'] != 'ro' || $receiver['country_code'] != 'ro') && $livrare['nr_colete'] > 1) {
            return false;
        }

        // sender
        $senderCountry = $api->findCountry(['isoAlpha2' => $sender['country_code']]);
        $senderSite = false;
        if($senderCountry && $senderCountry['requireState'] == true) {
            $senderState = $api->findState(['countryId' => $senderCountry['id'], 'name' => '']);
        }
        if($senderCountry && $senderCountry['siteNomen'] > 0) {
            $senderSite = $api->findSite(['countryId' => $senderCountry['id'], 'postCode' => $sender['postcode'], 'region' => $sender['county']]);
        }
        $senderStreet = false;
        // if($senderSite) {
        //     $senderStreet = $api->findStreet(['siteId' => $senderSite['id'], 'name' => $sender['street']]);
        // }
        $senderAddress = $this->createFullAddress($sender) ?? '';
            // ((isset($sender['street']) && $sender['street'] != '-' ? 'Strada '.$sender['street'] : '').
            // (isset($sender['street_nr']) && $sender['street_nr'] != '-' ? ' Nr. '.$sender['street_nr'] : '').
            // (isset($sender['bl_code']) ? ', Bl. '.$sender['bl_code'] : '').
            // (isset($sender['bl_letter']) ? ', Sc. '.$sender['bl_letter'] : '').
            // (isset($sender['intercom']) ? ', Interfon '.$sender['intercom'] : '').
            // (isset($sender['floor']) ? ', Etaj '.$sender['floor'] : '').
            // (isset($sender['apartment']) ? ', Ap./Nr. '.$sender['apartment'] : '')) ?? '';

        // receiver
        $receiverCountry = $api->findCountry(['isoAlpha2' => $receiver['country_code']]);
        $receiverSite = false;
        if($receiverCountry && $receiverCountry['requireState'] == true) {
            $receiverState = $api->findState(['countryId' => $receiverCountry['id'], 'name' => '']);
        }
        if($receiverCountry && $receiverCountry['siteNomen'] > 0) {
            $receiverSite = $api->findSite(['countryId' => $receiverCountry['id'], 'postCode' => $receiver['postcode'], 'region' => $receiver['county']]);
        }
        $receiverStreet = false;
        // if($receiverSite && $receiver['street'] != '-') {
        //     $receiverStreet = $api->findStreet(['siteId' => $receiverSite['id'], 'name' => $receiver['street']]);
        // }
        $receiverAddress = $this->createFullAddress($receiver) ?? '';
            // ((isset($receiver['street']) && $receiver['street'] != '-' ? 'Strada '.$receiver['street'] : '').
            // (isset($receiver['street_nr']) && $receiver['street_nr'] != '-' ? ' Nr. '.$receiver['street_nr'] : '').
            // (isset($receiver['bl_code']) ? ', Bl. '.$receiver['bl_code'] : '').
            // (isset($receiver['bl_letter']) ? ', Sc. '.$receiver['bl_letter'] : '').
            // (isset($receiver['intercom']) ? ', Interfon '.$receiver['intercom'] : '').
            // (isset($receiver['floor']) ? ', Etaj '.$receiver['floor'] : '').
            // (isset($receiver['apartment']) ? ', Ap./Nr. '.$receiver['apartment'] : '')) ?? '';


        $receiverAddress = isset($receiver['street']) && $receiver['street'] === '-'
            ? ($receiver['more_information'] ?? '') 
            : $receiverAddress;

        $livrare->api_shipment_id = $api->setOrder([
            'sender' => [
                'phone1' => $sender['phone'],
                'phone2' => $sender['phone_2'],
                'privatePerson' => isset($sender['company']) ? true : false,
                'clientName' => $sender['company'] ?? $sender['name'],
                'contactName' => $sender['name'] ?? '',
                'email' => $sender['email'],
                'countryId' => $senderCountry['id'],
                'siteId' => $senderSite != false ? $senderSite['id'] : null,
                'siteName' => $senderSite == false ? $sender['locality'] : null,
                'siteType' => $senderSite == false ? $senderCountry['siteNomen'] : null,
                'postCode' => $sender['postcode'],
                'streetId' => $senderStreet != false ? $senderStreet['id'] : null,
                'streetName' => $senderStreet == false ? $sender['street'] : null,
                'streetNo' => $sender['street_nr'] ?? '',
                'entranceNo' => substr((isset($sender['bl_code']) && $sender['bl_code'] ? 'Bl:'.$sender['bl_code'] : '').(isset($sender['bl_letter']) && $sender['bl_letter'] ? 'Sc:'.$sender['bl_letter'] : ''), 0, 10),
                'entranceNo' =>  '',
                'floorNo' => $sender['floor'] ?? '',
                'apartmentNo' => $sender['apartment'] ?? '',
                'addressNote' => $sender['landmark'] ?? $sender['more_information'] ?? '',
                'addressLine1' => strlen($senderAddress) > 35 ? $this->halfText($senderAddress)[0] : $senderAddress,
                'addressLine2' => strlen($senderAddress) > 35 ? $this->halfText($senderAddress)[1] : $senderAddress,
            ],
            'recipient' => [
                'phone1' => $receiver['phone'],
                'phone2' => $receiver['phone_2'],
                'privatePerson' => isset($receiver['company']) ? true : false,
                'clientName' => $receiver['company'] ?? $receiver['name'],
                'contactName' => $receiver['name'] ?? '',
                'email' => $receiver['email'],
                'countryId' => $receiverCountry['id'],
                'siteId' => $receiverSite != false ? $receiverSite['id'] : null,
                'siteName' => $receiverSite == false ? $receiver['locality'] : null,
                'siteType' => $receiverSite == false ? $receiverCountry['siteNomen'] : null,
                'postCode' => $receiver['postcode'],
                'streetId' => isset($receiverStreet) && $receiverStreet != false ? $receiverStreet['id'] : null,
                'streetName' => isset($receiverStreet) && $receiverStreet != false ? null : ($receiver['street'] !== '-' ? $receiver['street'] : null),
                'streetNo' => $receiver['street_nr'] ?? '',
                'entranceNo' =>  '',
                'entranceNo' => substr((isset($receiver['bl_code']) && $receiver['bl_code'] ? 'Bl:'.$receiver['bl_code'] : '').(isset($receiver['bl_letter']) && $receiver['bl_letter'] ? 'Sc:'.$receiver['bl_letter'] : ''), 0, 10),
                'floorNo' => $receiver['floor'] ?? '',
                'apartmentNo' => $receiver['apartment'] ?? '',
                'addressNote' => $receiver['landmark'] ?? $receiver['more_information'] ?? '',
                'addressLine1' => strlen($receiverAddress) > 35 ? $this->halfText($receiverAddress)[0] : $receiverAddress,
                'addressLine2' => strlen($receiverAddress) > 35 ? $this->halfText($receiverAddress)[1] : $receiverAddress,
            ],
            'pickupDate' => $livrare['pickup_day'] instanceof Carbon 
                ? $livrare['pickup_day']->format('Y-m-d') 
                : $livrare['pickup_day'],
            'pickUpStartTime' => $livrare['start_pickup_hour'],
            'visitEndTime' => $livrare['end_pickup_hour'],
            'saturdayDelivery' => $livrare['work_saturday'] ? true : false,
            'declaredValue' => $livrare['assurance'] ?? null,
            'ramburs' => $livrare['ramburs'],
            'rambursValue' => $livrare['ramburs_value'] ?? 0,
            'iban' => $livrare['iban'] ?? '',
            'accountHolder' => $livrare['titular_cont'] ?? '',
            'obpd' => $livrare['open_when_received'] ? true : false,
            // 'rod' => $livrare['retur_document'] ? true : false,
            'swap' => $livrare['retur_document'] ? true : false,
            'swap_parcels' => $livrare['swap_details']['nr_parcels'] ?? '',
            'parcelsCount' => $livrare['nr_colete'] ?? 1,
            'totalWeight' => $livrare['total_weight'] ?? 1,
            'contents' => $livrare['content'],
            'ref' => $livrare['customer_reference'],
            'parcels' => $parcels,
            'serviceId' => $receiver['country_code'] != 'ro'
                ? $api->getServiceCode($receiver['country_code'] ?? 'ro')
                : $api->getServiceCode($sender['country_code'] ?? 'ro', false),
            'currencyCode' => $receiver['country_code'] != 'ro'
                ? Country::code($receiver['country_code'] ?? 'ro')->currency_code
                : Country::code($sender['country_code'] ?? 'ro')->currency_code,
        ]);

        $livrare->api_shipment_awb = $livrare->api_shipment_id;
        $livrare->save();

        $receiver['full_address'] = $receiverAddress;
        $this->creareRepayment($livrare, $receiver);
    }

    public function createGLSOrder(Livrare $livrare, $curier, $parcels) 
    {
        $api = app(CourierGateway::class, ['type' => 3]);

        $sender = $livrare->sender;
        $receiver = $livrare->receiver;

        $senderAddress =
            (isset($sender['street']) && $sender['street'] != '-' ? 'Strada '.$sender['street'] : '').
            (isset($sender['street_nr']) && $sender['street_nr'] != '-' ? ' Nr. '.$sender['street_nr'] : '').
            (isset($sender['bl_code']) ? 'Bl. '.$sender['bl_code'] : '').
            (isset($sender['bl_letter']) ? ', Sc. '.$sender['bl_letter'] : '').
            (isset($sender['intercom']) ? ', Interfon '.$sender['intercom'] : '').
            (isset($sender['floor']) ? ', Etaj '.$sender['floor'] : '').
            (isset($sender['apartment']) ? ', Ap./Nr. '.$sender['apartment'] : '');
        $receiverAddress =
            (isset($receiver['street']) && $receiver['street'] != '-' ? 'Strada '.$receiver['street'] : '').
            (isset($receiver['street_nr']) && $receiver['street_nr'] != '-' ? ' Nr. '.$receiver['street_nr'] : '').
            (isset($receiver['bl_code']) ? 'Bl. '.$receiver['bl_code'] : '').
            (isset($receiver['bl_letter']) ? ', Sc. '.$receiver['bl_letter'] : '').
            (isset($receiver['intercom']) ? ', Interfon '.$receiver['intercom'] : '').
            (isset($receiver['floor']) ? ', Etaj '.$receiver['floor'] : '').
            (isset($receiver['apartment']) ? ', Ap./Nr. '.$receiver['apartment'] : '');


        $response = $api->setOrder([
            'Count' => $livrare['nr_colete'] ?? 1,
            'ramburs' => $livrare['ramburs'] ?? 1,
            'CODAmount' => $livrare['ramburs'] > 1 ? ($livrare['ramburs_value'] ?? 0) : 0,
            'CODReference' => $livrare->id,    // must be asked if it must be obtained from GLS
            'Content' => $livrare['content'],
            'PickupDate' => $livrare['pickup_day'] instanceof Carbon 
                ? $livrare['pickup_day']->format('Y-m-d') 
                : $livrare['pickup_day'],
            'PickupAddress' => [
                'Name' => $sender['company'] ?? $sender['name'],
                'ContactName' => $sender['name'] ?? '',
                'ContactPhone' => $sender['phone'],
                'ContactEmail' => $sender['email'] ?? '',
                'HouseNumber' => '',
                'HouseNumberInfo' => $senderAddress ?? '',
                'City' => $sender['locality'],
                'Street' => $sender['street'] ?? '',
                'ZipCode' => $sender['postcode'],
                'CountryIsoCode' => $sender['country_code'] ?? 'RO'
            ],
            'DeliveryAddress' => [
                'Name' => $receiver['company'] ?? $receiver['name'],
                'ContactName' => $receiver['name'] ?? '',
                'ContactPhone' => $receiver['phone'],
                'ContactEmail' => $receiver['email'] ?? '',
                'HouseNumber' => '',
                'HouseNumberInfo' => $receiverAddress ?? '',
                'City' => $receiver['locality'],
                'Street' => $receiver['street'] ?? '-',
                'ZipCode' => $receiver['postcode'] ?? '',
                'CountryIsoCode' => $receiver['country_code'] ?? 'RO'
            ],
            'TimeFrom' => $livrare['start_pickup_hour'],
            'TimeTo' => $livrare['end_pickup_hour'],
            'saturdayDelivery' => $livrare['work_saturday'] != null ? true : false,
            'declaredValue' => $livrare['assurance'] ?? false,
            'rod' => false,
            'swap' => $livrare['retur_document'] ? true : false,
            'public' => $curier->type == 1 ? true : false,
        ]);

        if($response === false) {
            $livrare->status = 5;
            $livrare->save();
            return back()->withErrors(['error' => __('Informatiile trimise prin api nu au fost corecte, contactati un admin.')]);
        } else {
            $livrare->api_shipment_id = $response['id'];
            $livrare->api_shipment_awb = $response['id'];
            $livrare->save();
        }

        $receiver['full_address'] = $receiverAddress;
        $this->creareRepayment($livrare, $receiver);
    }

    public function creareRepayment(Livrare $livrare, $receiver)
    {
        if($livrare->ramburs > 1 && $livrare->api_shipment_awb) {
            Repayment::create([
                'user_id' => $livrare->user_id,
                'awb' => $livrare->api_shipment_awb,
                // 'type' => $livrare->ramburs == "2" ? 1 : ( $livrare->iban != null ? 2 : 3 ),
                'type' => 1,
                'ref_client' => $livrare->customer_reference,
                'date_order' => $livrare->created_at,
                'date_delivered' => null,
                'payer_name' => $receiver['company'] ?? $receiver['name'],
                'payer_address' => $receiver['full_address'] ?? $receiver['more_information'] ?? '',
                'titular_cont' => $livrare->titular_cont,
                'iban' => $livrare->iban,
                'payed_on' => null,
                'total' => $livrare->ramburs_value,
            ]);
        }
    }


    public function newCreateOrder(Livrare $livrare)
    {
        $contacts = $livrare->contacts;
        $sender = $contacts->where('type','1')->first();
        $receiver = $contacts->where('type','2')->first();
        $packages = $livrare->type == '1' ? $livrare->packages : null;

        if(
            $sender && $receiver && (($livrare->type == '1' && $packages) || $livrare->type != '1')
        ) {
            $data = $livrare->toArray() + [
                'sender' => $sender ? $sender->toArray() : null,
                'receiver' => $receiver ? $receiver->toArray() : null,
            ] + ($livrare->type == '1' && $packages ? [
                'parcels' => $packages->toArray(),
            ] : []);

            $api = app(CourierGateway::class, ['type' => 3]);
            $response = $api->setOrder($data);

            if($response['status'] == 200) {
                $livrare->update([
                    'api_shipment_awb' => $response['awb'],
                    'api_shipment_id' => $response['awb']
                ]);
                $receiver['full_address'] = $this->createFullAddress($receiver);
                $this->creareRepayment($livrare, $receiver);
                $valid = true;
            } else {
                $msg = __('Informatiile pentru expediere nu au fost corecte. Va rog sa verificati si sa incercati din nou.');
            }
        }
        return [
            'valid' => isset($valid) ? $valid : false,
        ] + (!isset($valid) || !$valid ? [
            'message' => isset($msg) ? $msg : __('Informatiile pentru crearea unei livrari nu au putut fi salvate corect. Va rog incercati inca o data.')
        ] : []);
    }

    public function createFullAddress($address)
    {
        return (isset($address['street']) && $address['street'] != '-' ? 'Strada '.$address['street'] : '').
            (isset($address['street_nr']) && $address['street_nr'] != '-' ? ' Nr. '.$address['street_nr'] : '').
            (isset($address['bl_code']) ? ', Bl. '.$address['bl_code'] : '').
            (isset($address['bl_letter']) ? ', Sc. '.$address['bl_letter'] : '').
            (isset($address['intercom']) ? ', Interfon '.$address['intercom'] : '').
            (isset($address['floor']) ? ', Etaj '.$address['floor'] : '').
            (isset($address['apartment']) ? ', Ap./Nr. '.$address['apartment'] : '');
    }

    public function halfText($input)
    {
        $middle = ceil(strlen($input) / 2);
        $middle_space = strpos($input, " ", $middle - 1);

        if ($middle_space === false) {
            //there is no space later in the string, so get the last sapce before the middle
            $first_half = substr($input, 0, $middle);
            $middle_space = strpos($first_half, " ");
        }

        if ($middle_space === false) {
            //the whole string is one long word, split the text exactly in the middle
            $first_half = substr($input, 0, $middle);
            $second_half = substr($input, $middle);
        }
        else {
            $first_half = substr($input, 0, $middle_space);
            $second_half = substr($input, $middle_space);
        }
        return array(trim($first_half), trim($second_half));
    }

    private function normalize_judet($judet) {
        // Inlocuieste diacriticele
        $diacritice = ['ă', 'â', 'î', 'ș', 'ț', 'Ă', 'Â', 'Î', 'Ș', 'Ț'];
        $fara_diacritice = ['a', 'a', 'i', 's', 't', 'A', 'A', 'I', 'S', 'T'];
        $judet = str_replace($diacritice, $fara_diacritice, $judet);

        // Returneaza cu majuscule pentru mapare
        return strtoupper($judet);
    }

    private function cod_judet($nume_judet) {
        $map_judete = [
            'ALBA' => 'AB',
            'ARAD' => 'AR',
            'ARGES' => 'AG',
            'BACAU' => 'BC',
            'BIHOR' => 'BH',
            'BISTRITA-NASAUD' => 'BN',
            'BOTOSANI' => 'BT',
            'BRASOV' => 'BV',
            'BRAILA' => 'BR',
            'BUZAU' => 'BZ',
            'CARAS-SEVERIN' => 'CS',
            'CALARASI' => 'CL',
            'CLUJ' => 'CJ',
            'CONSTANTA' => 'CT',
            'COVASNA' => 'CV',
            'DAMBOVITA' => 'DB',
            'DOLJ' => 'DJ',
            'GALATI' => 'GL',
            'GIURGIU' => 'GR',
            'GORJ' => 'GJ',
            'HARGHITA' => 'HR',
            'HUNEDOARA' => 'HD',
            'IALOMITA' => 'IL',
            'IASI' => 'IS',
            'ILFOV' => 'IF',
            'MARAMURES' => 'MM',
            'MEHEDINTI' => 'MH',
            'MURES' => 'MS',
            'NEAMT' => 'NT',
            'OLT' => 'OT',
            'PRAHOVA' => 'PH',
            'SATU MARE' => 'SM',
            'SALAJ' => 'SJ',
            'SIBIU' => 'SB',
            'SUCEAVA' => 'SV',
            'TELEORMAN' => 'TR',
            'TIMIS' => 'TM',
            'TULCEA' => 'TL',
            'VASLUI' => 'VS',
            'VALCEA' => 'VL',
            'VRANCEA' => 'VN',
            'BUCURESTI' => 'B'
        ];

        $nume_judet_normalizat = $this->normalize_judet($nume_judet);
        return $map_judete[$nume_judet_normalizat] ?? null;
    }

    public function createTwoShipOrder($livrare, $curier, $parcels)
    {
        $api = app(CourierGateway::class, ['type' => 5]);
    
        $sender = $livrare->sender;
        $receiver = $livrare->receiver;
    
        $senderAddress =
            (isset($sender['street']) && $sender['street'] !== '-' ? 'Strada '.$sender['street'] : '').
            (isset($sender['street_nr']) && $sender['street_nr'] !== '-' ? ' Nr. '.$sender['street_nr'] : '').
            (isset($sender['bl_code']) ? ' Bl. '.$sender['bl_code'] : '').
            (isset($sender['bl_letter']) ? ', Sc. '.$sender['bl_letter'] : '').
            (isset($sender['intercom']) ? ', Interfon '.$sender['intercom'] : '').
            (isset($sender['floor']) ? ', Etaj '.$sender['floor'] : '').
            (isset($sender['apartment']) ? ', Ap./Nr. '.$sender['apartment'] : '');
    
        $receiverAddress =
            (isset($receiver['street']) && $receiver['street'] !== '-' ? 'Strada '.$receiver['street'] : '').
            (isset($receiver['street_nr']) && $receiver['street_nr'] !== '-' ? ' Nr. '.$receiver['street_nr'] : '').
            (isset($receiver['bl_code']) ? ' Bl. '.$receiver['bl_code'] : '').
            (isset($receiver['bl_letter']) ? ', Sc. '.$receiver['bl_letter'] : '').
            (isset($receiver['intercom']) ? ', Interfon '.$receiver['intercom'] : '').
            (isset($receiver['floor']) ? ', Etaj '.$receiver['floor'] : '').
            (isset($receiver['apartment']) ? ', Ap./Nr. '.$receiver['apartment'] : '');
    
        $pickupDate = $livrare['pickup_day'] instanceof Carbon
            ? $livrare['pickup_day']->setTimezone(config('app.timezone'))->startOfDay()->addHours(10)->format('Y-m-d\TH:i:s.000')
            : Carbon::parse($livrare['pickup_day'])->setTimezone(config('app.timezone'))->startOfDay()->addHours(10)->format('Y-m-d\TH:i:s.000');
    
        $packages = [];
    
        foreach ($parcels as $parcel) {
            $packages[] = [
                "Weight" => $parcel['weight'] ?? 1,
                "Length" => $parcel['length'] ?? 10,
                "Width" => $parcel['width'] ?? 10,
                "Height" => $parcel['height'] ?? 10,
                "Packaging" => "NotSet"
            ];
        }
    
        $payload = [
            "CarrierId" => $curier->meta('special_2ship_carrier_id') ?? 0,
            "Sender" => [
                "PersonName" => $sender['name'] ?? '',
                "CompanyName" => $sender['company'] ?? $sender['name'],
                "Country" => strtoupper($sender['country_code'] ?? 'RO'),
                "State" => $this->cod_judet($sender['county']),
                "City" => $sender['locality'],
                "PostalCode" => $sender['postcode'],
                "Address1" => $senderAddress,
                "Telephone" => $sender['phone'],
                "Email" => $sender['email'] ?? '',
                "IsResidential" => false
            ],
            "Recipient" => [
                "PersonName" => $receiver['name'] ?? '',
                "CompanyName" => $receiver['company'] ?? $receiver['name'],
                "Country" => strtoupper($receiver['country_code'] ?? 'RO'),
                "State" => $this->cod_judet($receiver['county']),
                "City" => $receiver['locality'],
                "PostalCode" => $receiver['postcode'],
                "Address1" => $receiverAddress,
                "Telephone" => $receiver['phone'],
                "Email" => $receiver['email'] ?? '',
                "IsResidential" => false
            ],
            "PickupDate" => $pickupDate,
            "PickupRequest" => [
                "RequestAPickup" => true,
                "ReadyTime" => $livrare['start_pickup_hour'] ?? '10:00',
                "CompanyCloseTime" => $livrare['end_pickup_hour'] ?? '18:00'
            ],
            "Packages" => $packages,
            "Contents" => [
                "Documents" => [
                    [
                        "Description" => $livrare['content'] ?? 'Documente',
                        "IsDutiable" => false
                    ]
                ]
            ],
            "ShipmentReference" => (string) $livrare->id,
            "ShipmentProtection" => $livrare['assurance'] ?? 0,
            "ShipmentProtectionCurrency" => "RON",
            "Billing" => [
                "BillingType" => "Prepaid"
            ]
        ];

        $shipOptions = [];
        $applyRateOptions = [];
        $setIsReturn = true; // default: we will set IsReturn unless overridden by a special value

        $specialOptions = [
            'work_saturday',
            'require_awb',
            'open_when_received',
            'retur_document',
            'ramburs_cash',
            'ramburs_cont',
            'assurance'
        ];

        foreach ($specialOptions as $key) {
            $attrKey = '2ship_' . $key;
            $specialValue = $curier->getAttributes()[$attrKey] ?? null;

            if ($specialValue) {
                $decoded = json_decode($specialValue, true);
                if (isset($decoded['code'])) {
                    $shipOptions[] = [
                        'code' => $decoded['code'],
                        'value' => '1'
                    ];

                    if ($key === 'retur_document') {
                        $setIsReturn = false; // Skip setting IsReturn manually
                    }
                }
            } else {
                // Fallback logic
                switch ($key) {
                    case 'work_saturday':
                        $applyRateOptions['SaturdayDelivery'] = !empty($livrare['work_saturday']);
                        break;
                    case 'open_when_received':
                        $applyRateOptions['CheckOnDelivery'] = !empty($livrare['open_when_received']);
                        break;
                    case 'ramburs_cash':
                    case 'ramburs_cont':
                        if ($livrare['ramburs'] > 1) {
                            $applyRateOptions['CollectOnDelivery'] = [
                                'Amount' => $livrare['ramburs_value'] ?? 0
                            ];
                        }
                        break;
                    case 'retur_document':
                        // defer setting IsReturn after this loop
                        break;
                }
            }
        }

        if (!empty($shipOptions)) {
            $payload['ShipOptions'] = $shipOptions;
        }
        if (!empty($applyRateOptions)) {
            $payload["ApplyRate"] = true;
            $payload['ApplyRateOptions'] = $applyRateOptions;
        }
        if ($setIsReturn) {
            $payload['IsReturn'] = !empty($livrare['retur_document']);
        }

        $response = json_decode($api->setOrder($payload), true);
    
        if ($response === false) {
            $livrare->status = 5;
            $livrare->save();
            return back()->withErrors(['error' => __('Informațiile trimise prin API nu au fost corecte. Contactați un administrator.')]);
        } else {
            $livrare->api_shipment_id = $response['OrderNumber'];
            $livrare->api_shipment_awb = $response['HoldShipmentId'];
            $livrare->save();
        }
    
        $receiver['full_address'] = $receiverAddress;
        $this->creareRepayment($livrare, $receiver);
    }
}
