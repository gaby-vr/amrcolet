<?php 

namespace App\Courier\UrgentCargus;

use App\Courier\CourierGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DateTime;
use stdClass;
use SoapClient;
use SoapFault;
use Exception;

class UrgentCargusCourierGateway implements CourierGateway
{
	private $baseURL = 'https://urgentcargus.azure-api.net/api';
	private $username = null;
	private $password = null;
	private $price_table_id = null;
	private $tertiary_client_id = null;
	private $subscription_key = null;
	private $token = null;
	private $default_country_id = 1; // = Romania country id 

	public function __construct($array)
	{
		$this->username 		  = $array['username'] ?? env('URGENT_CARGUS_USERNAME');
		$this->password 		  = $array['password'] ?? env('URGENT_CARGUS_PASSWORD');
		$this->subscription_key   = $array['subscription_key'] ?? env('URGENT_CARGUS_SUBSCRIPTION_KEY');
		$this->price_table_id 	  = $array['price_table_id'] ?? env('URGENT_CARGUS_PRICE_TABLE_ID');
		$this->tertiary_client_id = $array['tertiary_client_id'] ?? env('URGENT_CARGUS_TERTIARY_CLIENT_ID');
	}

	public function setAll($array)
	{
		$this->username 		  = $array['username'];
		$this->password 		  = $array['password'];
		$this->subscription_key   = $array['subscription_key'];
		$this->price_table_id 	  = $array['price_table_id'];
		$this->tertiary_client_id = $array['tertiary_client_id'];
	}

	protected function switchCredentials(int $account = 1)
	{
		$this->username 		  = env('URGENT_CARGUS_USERNAME'.($account === 1 ? '' : '2'));
		$this->password 		  = env('URGENT_CARGUS_PASSWORD'.($account === 1 ? '' : '2'));
		$this->subscription_key   = env('URGENT_CARGUS_SUBSCRIPTION_KEY'.($account === 1 ? '' : '2'));
		$this->price_table_id 	  = env('URGENT_CARGUS_PRICE_TABLE_ID'.($account === 1 ? '' : '2'));
		$this->tertiary_client_id = env('URGENT_CARGUS_TERTIARY_CLIENT_ID'.($account === 1 ? '' : '2'));
	}

	protected function switchCondition(array $array = [])
	{
		return (empty($array['TotalWeight']) || $array['TotalWeight'] < (setare('CARGUS_ACCOUNT_SWITCH_WEIGHT') ?? 10)) 
			&& !empty($array['createdAt']) 
			&& $array['createdAt'] > '2025-03-18 22:51:00'
				? 2 : 1;
	}

	protected function getToken()
	{
		$requestURL = 'LoginUser';

		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'token' => true,
			'body' => [
				'UserName' => $this->username,
				'Password' => $this->password,
			],
		];
		$this->token = json_decode($this->connect($data), true);
		return is_array($this->token) ? json_encode($this->token) : $this->token;
	}

	protected function verifyToken()
	{
		$requestURL = 'TokenVerification';

		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'verify' => true,
			'body' => [],
		];
		return json_decode($this->connect($data), true);
	}

	protected function connect($array)
	{
	    $ch = curl_init();

	    $body = json_encode($array['body']);

	    $headers = [
    		0 => 'Ocp-Apim-Subscription-Key: '.$this->subscription_key,
    		1 => 'Ocp-Apim-Trace: true',
    		2 => 'Content-Type: application/json', 
    		3 => 'Content-Length: '.strlen($body),
    	] + (isset($array['token']) ? [] : [
    		4 => 'Authorization: Bearer '.$this->getToken(),
    	]);

	    curl_setopt_array($ch, [
	    	CURLOPT_URL => $array['url'],
	    	CURLOPT_CUSTOMREQUEST => $array['method'] ?? "POST",
	    	CURLOPT_HTTPHEADER => $headers,
	    	CURLOPT_POSTFIELDS => $body,
	    	CURLINFO_HEADER_OUT => true,
	    	CURLOPT_HEADER => 0,
	    	CURLOPT_RETURNTRANSFER => true,
	    	// Tries before timeout
	    	CURLOPT_CONNECTTIMEOUT => 2,
	    	// Timeout in seconds
	    	CURLOPT_TIMEOUT => 30
		]);

	    $output = curl_exec($ch);
	    $header = curl_getinfo($ch);

	    // if($header['http_code'] != '200') {
	    // 	Log::info('Error CURL Header');
		// 	Log::info($header);
	    // 	Log::info('Error CURL Response');
		// 	Log::info($output);
		// }

	    curl_close($ch);

	    return $output;
	}

	public function setOrder($array)
	{
		return $this->newSetOrder($array);
		/*if($array['BankRepayment'] != 0) {
			return $this->newSetOrder($array);
		}
		$ParcelCodes = [];
		if(isset($array['parcels']) && $array['parcels'] > 0) {
			$ParcelCodes = [[
		      	//"Code": "string",
		      	//"Type": 1, // 0: envelope, 1: package
		      	"Weight" => $array['ParcelCodes'][0]['weight'],
		      	"Length" => $array['ParcelCodes'][0]['length'],
		      	"Width" => $array['ParcelCodes'][0]['width'],
		      	"Height" => $array['ParcelCodes'][0]['height'],
		      	"ParcelContent" => $array['PackageContent']
		    ]];
		} else {
			$ParcelCodes = [[
		      	"Type" => 0,
		      	"ParcelContent" => $array['PackageContent']
		    ]];
		}
		
		$requestURL = 'AwbPickup/WithGetAwb';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'PickupStartDate' => $array['PickupStartDate'],
				'PickupEndDate' => $array['PickupEndDate'],
				'Sender' => [
					//'LocationId' => 0,
				    'Name' => $array['sender']['name'],
				    'CountyId' => $array['sender']['countyId'],
				    'CountyName' => $array['sender']['countyName'],
				    'LocalityId' => $array['sender']['localityId'],
				    'LocalityName' => $array['sender']['localityName'],
				    'StreetId' => $array['sender']['streetId'] ?? 0,
				    'StreetName' => $array['sender']['streetName'],
				    'BuildingNumber' => $array['sender']['buildingNumber'],
				    'AddressText' => $array['sender']['address'],
				    'ContactPerson' => $array['sender']['contactPerson'],
				    'PhoneNumber' => $array['sender']['phone'],
				    'Email' => $array['sender']['email'],
				    'CodPostal' => $array['sender']['postcode'],
				    'PostalCode' => $array['sender']['postcode'],
				    'CountryId' => $array['sender']['countryId']
				],
				'Recipient' => [
					//'LocationId' => 0,
				    'Name' => $array['receiver']['name'],
				    'CountyId' => $array['receiver']['countyId'],
				    'CountyName' => $array['receiver']['countyName'],
				    'LocalityId' => $array['receiver']['localityId'],
				    'LocalityName' => $array['receiver']['localityName'],
				    'StreetId' => $array['receiver']['streetId'] ?? 0,
				    'StreetName' => $array['receiver']['streetName'],
				    'BuildingNumber' => $array['receiver']['buildingNumber'], // street number
				    'AddressText' => $array['receiver']['address'],
				    'ContactPerson' => $array['receiver']['contactPerson'],
				    'PhoneNumber' => $array['receiver']['phone'],
				    'Email' => $array['receiver']['email'],
				    'CodPostal' => $array['receiver']['postcode'],
				    'PostalCode' => $array['receiver']['postcode'],
				    'CountryId' => $array['receiver']['countryId'],
				],
				'Parcels' => $array['parcels'] ?? 0,
				'Envelopes' => $array['envelops'] ?? 0,
				'ParcelCodes' => $ParcelCodes,
				'TotalWeight' => $array['TotalWeight'],
				'DeclaredValue' => $array['DeclaredValue'],
				'CashRepayment' => $array['CashRepayment'],
				'BankRepayment' => $array['BankRepayment'],
				'OtherRepayment' => $array['OtherRepayment'],
				'ServiceId' => 34,
				// 'PriceTableId' => $this->price_table_id,
				'ShipmentPayer' => 3,
				'TertiaryClientId' => $this->tertiary_client_id,
				'OpenPackage' => $array['OpenPackage'] ?? false,
				'MorningDelivery' => $array['SaturdayDelivery'] ?? false,
				'SaturdayDelivery' => $array['SaturdayDelivery'] ?? false,
				'PackageContent' => $array['PackageContent'],
			],
		];

		Log::info('Urgent Cargus data order:');
		Log::info($data);

		$response = json_decode($this->connect($data), true);
		Log::info($response);
		return is_array($response) && count($response) > 0 ? [ 'IdComanda' => $response[0]['IdComanda'] , 'BarCode' => $response[0]['BarCode'] ] : false ;*/
	}

	public function newSetOrder($array)
	{
		$ParcelCodes = [];
		if(isset($array['parcels']) && $array['parcels'] > 0) {
			$ParcelCodes = [[
		      	"Code" => 0,
		      	"Type" => 1, // 0: envelope, 1: package
		      	"Weight" => $array['ParcelCodes'][0]['weight'],
		      	"Length" => $array['ParcelCodes'][0]['length'],
		      	"Width" => $array['ParcelCodes'][0]['width'],
		      	"Height" => $array['ParcelCodes'][0]['height'],
		      	"ParcelContent" => $array['PackageContent']
		    ]];
		} else {
			$ParcelCodes = [[
		      	"Type" => 0,
		      	"ParcelContent" => $array['PackageContent']
		    ]];
		}

		$this->switchCredentials($this->switchCondition($array + ['createdAt' => now()->format('Y-m-d H:i:s')]));
		
		$requestURL = 'Awbs/WithGetAwb';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'PickupStartDate' => $array['PickupStartDate'],
				'PickupEndDate' => $array['PickupEndDate'],
				'Sender' => [
					'LocationId' => $this->getLocationId($array['sender']),
				],
				'Recipient' => [
					//'LocationId' => 0,
				    'Name' => $array['receiver']['name'],
				    'CountyId' => $array['receiver']['countyId'],
				    'CountyName' => $array['receiver']['countyName'],
				    'LocalityId' => $array['receiver']['localityId'],
				    'LocalityName' => $array['receiver']['localityName'],
				    // 'StreetId' => $array['receiver']['streetId'] ?? 0,
				    // 'StreetName' => $array['receiver']['streetName'],
				    // 'BuildingNumber' => $array['receiver']['buildingNumber'], // street number
				    'AddressText' => $array['receiver']['address'],
				    'ContactPerson' => $array['receiver']['contactPerson'],
				    'PhoneNumber' => $array['receiver']['phone'],
				    'Email' => $array['receiver']['email'],
				    'CodPostal' => $array['receiver']['postcode'],
				    'PostalCode' => $array['receiver']['postcode'],
				    'CountryId' => $array['receiver']['countryId'],
				],
				'Parcels' => $array['parcels'] ?? 0,
				'Envelopes' => $array['envelops'] ?? 0,
				'ParcelCodes' => $ParcelCodes,
				'TotalWeight' => $array['TotalWeight'],
				'DeclaredValue' => $array['DeclaredValue'],
				'CashRepayment' => $array['CashRepayment'],
				'BankRepayment' => $array['BankRepayment'],
				'OtherRepayment' => $array['OtherRepayment'],
				'ServiceId' => 34,
				'PriceTableId' => $this->price_table_id, // 151272
				'ShipmentPayer' => 3,
				'TertiaryClientId' => $this->tertiary_client_id, // 1044244621 // 1039005840,
				'OpenPackage' => $array['OpenPackage'] ?? false,
				'MorningDelivery' => $array['SaturdayDelivery'] ?? false,
				'SaturdayDelivery' => $array['SaturdayDelivery'] ?? false,
				'PackageContent' => $array['PackageContent'],
			],
		];
		if(!$array['receiver']['postcode']) {
			unset($data['body']['Recipient']['CodPostal']);
			unset($data['body']['Recipient']['PostalCode']);
		}
		if(isset($array['envelops']) && $array['envelops'] > 0) {
			$data['body']['ParcelCodes'] = [
				0 => [
					'code' => 0,
					'type' => 0,
                    'ParcelContent' => $array['PackageContent']
				]
			];
		}


		// Log::info('Urgent Cargus data order:');
		// Log::info($data);
		// Log::info($array['PickupStartDate'].'T'.str_pad($array['PickupStartHour'], 2, "0", STR_PAD_LEFT).':00:00');
		// Log::info($array['PickupEndDate'].'T'.str_pad($array['PickupEndHour'], 2, "0", STR_PAD_LEFT).':00:00');

		$response = json_decode($this->connect($data), true);
		// \Log::info("Raspuns:");
		// \Log::info($response); 
		// \Log::info("Data Cargus:");
		// \Log::info($data);
		if(
			!isset($response[0]['IdComanda'])
			|| $this->submitOrder([
				'locationId' => $data['body']['Sender']['LocationId'], 
				'PickupStartDate' => $array['PickupStartDate'].'T'.str_pad($array['PickupStartHour'], 2, "0", STR_PAD_LEFT).':00:00', 
				'PickupEndDate' => $array['PickupEndDate'].'T'.str_pad($array['PickupEndHour'], 2, "0", STR_PAD_LEFT).':00:00'
			]) != true
		) { \Log::info($response); \Log::info($data); return false; }
		return is_array($response) && count($response) > 0 ? [ 'IdComanda' => $response[0]['IdComanda'] , 'BarCode' => $response[0]['BarCode'] ] : false ;
	}

	public function calculateOrder($array)
	{
		$requestURL = 'ShippingCalculation';

		if(!isset($array['from']) || !isset($array['to'])) {
			return false;
		}

		$this->switchCredentials($this->switchCondition($array + ['createdAt' => now()->format('Y-m-d H:i:s')]));

		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				"FromLocalityId" => $array['from']['localityId'],
			  	"ToLocalityId" => $array['to']['localityId'],
			  	"FromCountyName" => $array['from']['countyName'] ?? '',
			  	"FromLocalityName" => $array['from']['localityName'] ?? '',
			  	"ToCountyName" => $array['to']['countyName'] ?? '',
			  	"ToLocalityName" => $array['to']['localityName'] ?? '',

			  	'Parcels' => $array['parcels'] ?? 0,
				'Envelopes' => $array['envelops'] ?? 0,
				'TotalWeight' => $array['TotalWeight'],
				'DeclaredValue' => $array['DeclaredValue'],
				'CashRepayment' => $array['CashRepayment'],
				'BankRepayment' => $array['BankRepayment'],
				'OtherRepayment' => $array['OtherRepayment'],
				'PaymentInstrumentId' => 0,
  				'PaymentInstrumentValue' => 0.0,
				'ServiceId' => 34,
				'PriceTableId' => $this->price_table_id,
				'ShipmentPayer' => 3,
				'OpenPackage' => $array['OpenPackage'] ?? false,
				'MorningDelivery' => $array['SaturdayDelivery'] ?? false,
				'SaturdayDelivery' => $array['SaturdayDelivery'] ?? false,
			],
		];

		$response = json_decode($this->connect($data), true);
		return is_array($response) && count($response) > 0 ? $response : false ;
	}

	public function getOrder($array)
	{
		$requestURL = 'Orders/GetByOrderId?orderId='.$array['orderId'];
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return is_array($response) && count($response) > 0 ? $response : false ;
	}

	public function getAwbByOrder($array)
	{
		$requestURL = 'Awbs?orderId='.$array['orderId'];
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return is_array($response) && count($response) > 0 ? $response : false ;
	}

	public function trackParcels($array)
	{
		$this->switchCredentials($this->switchCondition($array));
		$requestURL = 'AwbTrace?barCode='.$array['barCode'];
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return is_array($response) && $response != null && count($response) > 0 ? $response : false ;
	}

	public function trackOrder($array)
	{
		$requestURL = 'Orders/GetByOrderId?orderId='.$array['orderId'];
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return is_array($response) && count($response) > 0 ? $response : false ;
	}

	public function cancelOrder($array)
	{
		$requestURL = 'Orders?locationId='.$array['locationId'].'&PickupStartDate='.$array['PickupStartDate'].'&PickupEndDate='.$array['PickupEndDate'].'&action=0';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'PUT',
			'body' => [
				'locationId' => $array['locationId'],
				'PickupStartDate' => $array['PickupStartDate'],
				'PickupEndDate' => $array['PickupEndDate'],
				'action' => 0,
			],
		];

		$response = $this->connect($data);
		$response = json_decode($response, true);
		return is_array($response) && count($response) > 0 ? $response : true ;
	}

	public function submitOrder($array)
	{
		$requestURL = 'Orders?locationId='.$array['locationId'].'&PickupStartDate='.$array['PickupStartDate'].'&PickupEndDate='.$array['PickupEndDate'].'&action=1';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'PUT',
			'body' => [
				'locationId' => $array['locationId'],
				'PickupStartDate' => $array['PickupStartDate'],
				'PickupEndDate' => $array['PickupEndDate'],
				'action' => 1,
			],
		];
		$response = json_decode($this->connect($data), true);
		return is_array($response) && count($response) > 0 ? $response : true ;
	}

	public function printAWB($array)
	{
		$this->switchCredentials($this->switchCondition($array));
		$requestURL = 'AwbDocuments?barCodes='.$array['barcode'].'&type=PDF&format='.($array['format'] == 'A6' ? '10x14' : 'A4');
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [
				'barcode' => $array['barcode'],
				'type' => 'PDF',
				'format' => $array['format'] == 'A6' ? '10x14' : 'A4',
			],
		];
		$awb = base64_decode($this->connect($data));
		header('Content-Type: application/pdf');
		header('Content-Length: '.strlen($awb));
		header('Content-Disposition: attachment; filename="Cargus_AWB_'.$array['barcode'].'.pdf"');
		echo $awb;
	}

	public function getLocationId($array)
	{
		$locations = $this->getLocations();

		if($locations != false) {
			foreach ($locations as $location) {
				
				if (
		       		$location['LocalityId'] == $array['localityId'] 
		       		&& strtolower($location['AddressText']) == strtolower($array['address']) 
		       		&& $location['BuildingNumber'] == $array['buildingNumber']
		       	) {
		           	return $location['LocationId'];
		       	} elseif (
		       		isset($array['LocalityId']) && $location['LocalityId'] == $array['localityId'] 
		       		&& strtolower($location['StreetName']) == strtolower($array['streetName']) 
		       		&& $location['BuildingNumber'] == $array['buildingNumber']
		       	) {
		           	return $location['LocationId'];
		       	} elseif (
		       		$location['CountyId'] == $array['countyId'] 
		       		&& strtolower($location['LocalityName']) == strtolower($array['localityName']) 
		       		&& strtolower($location['StreetName']) == strtolower($array['streetName']) 
		       		&& $location['BuildingNumber'] == $array['buildingNumber']
		       	) {
		           	return $location['LocationId'];
		       	}
		   	}
		}

		// StreetName does not save if it does not have a StreetId
		$requestURL = 'PickupLocations';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'POST',
			'body' => [
				'AutomaticEOD' => null,
                'Name' => $array['name'],
			    'CountyId' => $array['countyId'],
			    'CountyName' => $array['countyName'],
			    'LocalityId' => $array['localityId'],
			    'LocalityName' => $array['localityName'],
			    // 'StreetId' => null,
			    // 'StreetName' => $array['streetName'],
			    // 'BuildingNumber' => $array['buildingNumber'],
			    'AddressText' => $array['address'],
			    'ContactPerson' => $array['contactPerson'],
			    'PhoneNumber' => $array['phone'],
			    'Email' => $array['email'],
			    'CodPostal' => $array['postcode']
            ],
		];

		$response = json_decode($this->connect($data), true);
		return $response != null ? $response : false ;
	}

	public function getLocations($array = [])
	{
		$requestURL = 'PickupLocations';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return $response != null && count($response) > 0 ? $response : false ;
	}

	public function getAWB($array)
	{
		$this->switchCredentials($this->switchCondition($array));
		$requestURL = 'AwbDocuments?barCodes='.$array['barcode'].'&type=PDF&format='.($array['format'] == 'A6' ? '10x14' : 'A4');
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [
				'barcode' => $array['barcode'],
				'type' => 'PDF',
				'format' => $array['format'] == 'A6' ? '10x14' : 'A4',
			],
		];
		return base64_decode($this->connect($data));
	}

	// returns orders with repayments even if they are not delivered
	public function getPayments($from, $to, $account = 1)
	{
		$this->switchCredentials($account);
		$requestURL = 'CashAccount/GetByDate?FromDate='.$this->formatDate($from).'&ToDate='.$this->formatDate($to);
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];

		$response = json_decode($this->connect($data), true);
		return $response != null && is_array($response) && count($response) > 0 ? $response : false;
	}

	// returns orders with repayments that are delivered and payment sent to client
	public function getPayment($date, $account = 1)
	{
		$this->switchCredentials($account);
		$requestURL = 'CashAccount/GetByDeductionDate?DeductionDate='.$this->formatDate($date);
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return $response != null && is_array($response) && count($response) > 0 ? $response : false;
	}

	public function findCountry($array)
	{
		$requestURL = 'Countries';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		if(gettype($response) == 'array') {
			foreach ($response as $country) {
				$iso = $country['Abbreviation'] ?? '';
		       	if (strtolower($iso) == $array['isoAlpha2']) {
		           return $country;
		       	}
		   }
		} 
		return false;
	}

	public function findCounty($array)
	{
		$requestURL = 'Counties?countryId='.$array['countryId'];
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		//return json_decode($this->connect($data), true);
		$response = json_decode($this->connect($data), true);
		if(gettype($response) == 'array') {
			foreach ($response as $county) {
		       if (strtolower($county['Name']) == strtolower($array['name']) || strtolower($county['Name']) == strtolower(str_replace('-', ' ', $array['name'])) || strtolower($county['Name']) == strtolower(str_replace(' ', '-', $array['name']))) {
		           return $county;
		       }
		   }
		} 
		return false;
	}

	/*locality:11 [▼
	  "LocalityId" => 170
	  "Name" => "SUCEAVA"
	  "ParentId" => 170
	  "ParentName" => "SUCEAVA"
	  "ExtraKm" => 0
	  "InNetwork" => false
	  "CountyId" => 37
	  "CountryId" => 1
	  "PostalCode" => "0"
	  "MaxHour" => "17:00"
	  "SaturdayDelivery" => true
	]*/

	public function findLocality($array)
	{
		$requestURL = 'Localities?countryId='.$array['countryId'].'&countyId='.$array['countyId'];
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		if(gettype($response) == 'array') {
			foreach ($response as $locality) {
		       if (strtolower($locality['Name']) == strtolower($array['name']) || strtolower($locality['Name']) == strtolower(str_replace('-', ' ', $array['name'])) || strtolower($locality['Name']) == strtolower(str_replace(' ', '-', $array['name']))) {
		           return $locality;
		       }
		   }
		} 
		return false;

		// return count($response) > 0 ? $response[0] : false ;
	}

	public function findStreet($array)
	{
		$requestURL = 'Streets?localityId='.$array['localityId'];
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		if(gettype($response) == 'array') {
			foreach ($response as $street) {
				foreach ($street['PostalNumbers'] as $postcodes) {
			       	if ($postcodes['PostalCode'] == $array['postcode']) {
			           return $street;
			       	}
			    }
		   }
		} 
		return false;

		// return count($response) > 0 ? $response[0] : false ;
	}

	public function getLocality($array)
	{
		$requestURL = 'Localities/DetailsLocality?LocalityId='.$array['localityId'];
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function getPrices($array, $account = 1)
	{
		$requestURL = 'PriceTables';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'method' => 'GET',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function formatDate($date, int $hours = 0)
	{
		$date = $date instanceof Carbon ? $date : Carbon::parse($date);
		return $date->timezone(config('app.timezone'))->startOfDay()->addHours($hours)->format('Y-m-d');
	}
}