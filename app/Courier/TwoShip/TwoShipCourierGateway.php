<?php

namespace App\Courier\TwoShip;

use App\Courier\CourierGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TwoShipCourierGateway implements CourierGateway
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('2SHIP_API_URL'), '/') . '/';
        $this->apiKey = env('2SHIP_API_KEY');
    }

    public function setAll($array)
    {
        $this->baseUrl = $array['2SHIP_API_URL'] ?? rtrim(env('2SHIP_API_URL'), '/') . '/';
        $this->apiKey = $array['2SHIP_API_KEY'] ?? env('2SHIP_API_KEY');
    }

    protected function connect($endpoint, $body = [], $method = 'POST')
    {
	    $headers = [
            'Authorization: User-WS-Key ' . $this->apiKey,
	    	'Content-Type: application/json'
	    ];

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_HEADER, 0);

		// curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if ($method == 'POST') {
            $body['WS_Key'] = $this->apiKey;
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    // Timeout in seconds
	    curl_setopt($ch, CURLOPT_TIMEOUT, 600);

	    $output = curl_exec($ch);

	    curl_close($ch);

	    return $output;
    }

    public function setOrder($array)
    {
        return $this->connect('Hold', $array);
    }

	public function printAWB($array)
	{
        $courierName = $array['carrierName'];
		$awb = $this->getAWB($array);
		if($awb) {
			header('Content-Type: application/pdf');
			header('Content-Length: '.strlen($awb));
			header('Content-Disposition: attachment; filename="'.$courierName.'_AWB_'.$array['awb'].'.pdf"');
			echo $awb; exit();
		} else {
			abort(404);
		}
	}

	public function getAWB($array)
	{
		$awb = LivrareAwb::where('api', 5)->where('api_awb', $array['awb'])->first();
		if($awb) {
			return implode(array_map('chr', json_decode($awb->awb, true)));
		} elseif(isset($array['return'])) {
			return false;
		} else {
			abort(404);
		}
	}

    public function pickupOrder($array)
    {
        $payload = [
            'PickupRequest' => [
                'Carrier' => $array['carrier'] ?? '',
                'PickupDate' => $this->formatDate($array['pickup_date'] ?? now()),
                'ReadyTime' => $array['ready_time'] ?? '10:00',
                'CloseTime' => $array['close_time'] ?? '18:00',
                'Location' => $array['location'] ?? '',
                'Packages' => $array['packages'] ?? [],
                'Contact' => [
                    'Name' => $array['contact_name'],
                    'Phone' => $array['contact_phone'],
                ],
                'Address' => [
                    'Address1' => $array['address']['address1'],
                    'City' => $array['address']['city'],
                    'State' => $array['address']['state'] ?? '',
                    'PostalCode' => $array['address']['postal_code'],
                    'Country' => $array['address']['country'] ?? 'RO',
                ],
            ]
        ];

        return $this->connect('CreatePickupRequest', $payload);
    }

    public function calculateOrder($array)
    {
/* EXISTA SAU NU EXISTA SERVICIUL => restul va fi validat de logica existenta pe curierii definiti in admin */
        // dd($array);
        $response = $this->connect('RateSummaryAllCarriers', $array['payload']);

        $rates = json_decode($response, true);
        // dd($rates);

        if (is_array($rates)) {
            if (isset($rates["Message"])) {
                return false;
            }
            foreach ($rates as $rateCarrier) {
                if ((int) $rateCarrier['CarrierId'] === $array['carrierId']) {
                    $services = $rateCarrier['Services'] ?? [];

                    if (!empty($services)) {
                        // selectează cel mai ieftin serviciu disponibil
                        $lowest = collect($services)->filter(function ($s) {
                            return empty($s['ERROR']);
                        })->sortBy('TotalCustomerPriceInSelectedCurrency')->first();

                        if ($lowest) {
                            return true;
                        }
                    }
                    break;
                }
            }
        }

        return false;
    }

    public function cancelOrder($array)
    {
		$body = [
            "ShipmentId" => $array['awb'],
            "DeleteType" => "ByShipmentId",
            "DeleteFromOnHold" => true,
        ];
		$response = json_decode($this->connect('DeleteShipment', $body), true);
		return isset($response['Success']) ? true : $response;
    }

    public function cancelOnHoldOrder($array)
    {
		$body = [
            "OrderNumber" => $array['OrderNumber'],
            "DeleteType" => "ByOrderNumber",
        ];
		$response = json_decode($this->connect('DeleteOnHoldOrder', $body), true);
		return isset($response['DeletedOrders']) && count($response['DeletedOrders']) > 0 ? true : $response;
    }

    public function trackParcels($array)
    {
		$body = [
            "TrackingNumber" => $array['AWB'],
            "FindBy" => "ByTrackingNumber",
        ];
		$response = json_decode($this->connect('Tracking', $body), true);
		return isset($response['TrackingStatusCode']) ? $response : false;
    }

    public function getOrder($id)
    {
        return $this->connect('ShipmentDetail', ['ShipmentID' => $id]);
    }

    public function getOrderParcels($id)
    {
        return [];
    }

    public function findCountry($query)
    {
        return [];
    }

    public function findStreet($query)
    {
        return [];
    }

    public function getPayments()
    {
        return [];
    }

    public function formatDate($date, int $hours = 0): string
    {
        $timezone = config('app.timezone');
    
        $date = $date instanceof Carbon ? $date : Carbon::parse($date, $timezone);
    
        return $date
            ->setTimezone($timezone)
            ->startOfDay()
            ->addHours($hours)
            ->format('Y-m-d\TH:i:s') . '.000';
    }
}
