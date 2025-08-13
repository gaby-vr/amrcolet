<?php 

namespace App\Courier\GLS;

use App\Courier\CourierGateway;
use App\Models\LivrareAwb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DateTime;
use stdClass;
use SoapClient;
use SoapFault;
use Exception;

class GLSCourierGateway implements CourierGateway
{
	private $baseURL = 'https://api.mygls.ro'; // https://api.mygls.ro/.
	private $testURL = 'https://api.test.mygls.ro'; // https://api.test.mygls.ro/.
	private $client_number = null;
	private $username = null;
	private $password = null;
	private $sandbox = false;
	private $services = [
		'24H',	// Service guaranteed delivery shipment in 24 Hours
		'ADR',	// Agreement about Dangerous goods by Road
		'AOS',	// Addressee Only Service
		'COD',	// Cash On Delivery service /ok
		'CS1',	// Contact Service
		'DDS',	// Day Definite Service
		'DPV',	// Declared Parcel Value service
		'FDS',	// Flexible Delivery Service /ok de la sine
		'FSS',	// Flexible delivery Sms Service /ok se tacseaza
		'INS',	// Insurance Service
		'PRS',	// Pick and Return Service /ok de la expeditor la un punct fix salvat in cont
		'PSD',	// Parcel Shop Delivery service
		'PSS',	// Pick & Ship Service /ok la orice adresa la orice adresa
		'SAT',	// SATurday service
		'SBS',	// Stand By Service
		'SDS',	// Scheduled Delivery Service

		'SM1',	// SMs service
		'SM2',	// SMs pre-advice
		'SRS',	// 
		'SZL',	// document return service
		'T09',	// Express service
		'T10',	// Express service
		'T12',	// Express service
		'TGS',	// Think Green Service
		'XS',	// Exchange Service
	];
	// greutate max 31.5kg
	// plata unui ramburs se face in maxim 48h si se va adauga in borderou
	private $details = null;
	private $defaultCountryIso = 'RO';

	public function __construct($array)
	{
		$this->sandbox 		 = $array['sandbox'] ?? env('GLS_SANDBOX', false);
		$this->username 	 = $array['username'] ?? env('GLS_USERNAME');
		$this->password 	 = $array['password'] ?? env('GLS_PASSWORD');
		$this->client_number = $array['client_number'] ?? env('GLS_CLIENT_NUMBER');
		if($this->sandbox) {
			$this->username 	 = $array['username'] ?? env('GLS_USERNAME_TEST');
			$this->password 	 = $array['password'] ?? env('GLS_PASSWORD_TEST');
			$this->client_number = $array['client_number'] ?? env('GLS_CLIENT_NUMBER_TEST');
			$this->baseURL 		 = $this->testURL;
		}
		if(isset($array['details'])) {	
			$this->details 	= $array['details'];
		}
		$this->password = array_values(unpack('C*', hash('sha512', $this->password, true)));

	}

	public function setAll($array)
	{
		$this->sandbox = $array['sandbox'];
		$this->username = $array['username'];
		$this->password = $array['password'];
		$this->client_number = $array['client_number'];
		$this->details 	= $array['details'];
	}

	protected function connect($array)
	{
	    $body = json_encode($array['body']);
	    $headers = $array['headers'] ?? [
	    	'Content-Type: application/json',
	    	'Content-Length: '.strlen($body)
	    ];

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $array['url']);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_HEADER, 0);

		// curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $array['method'] ?? "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    // Timeout in seconds
	    curl_setopt($ch, CURLOPT_TIMEOUT, 600);

	    $output = curl_exec($ch);

	    curl_close($ch);

	    return $output;
	}

	public function setOrder($array)
	{
		$requestURL = 'ParcelService.svc/json/PrepareLabels';

		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'Username' => $this->username,
				'Password' => $this->password,
				'ParcelList' => [[
					// 'language' => $array['language'] ?? 'ro',
					'ClientNumber' => $this->client_number,
              		'ClientReference' => $array['ClientReference'] ?? null,
					'Count' => $array['Count'] > 0 ? $array['Count'] : 1,
					'CODAmount' => $array['CODAmount'] ?? 0,
					'CODReference' => $array['CODReference'] ?? '',	// its filled by us with something to identify the COD order
					'Content' => $array['Content'],
					'PickupDate' => '/Date('.(strtotime($array['PickupDate']) * 1000).')/',
					// 'PickupDate' => '/Date('.(strtotime('2024-03-19 08:30:00') * 1000).')/',
					'PickupAddress' => [
	      				'Name' => $array['PickupAddress']['Name'],
	      				'ContactName' => $array['PickupAddress']['ContactName'] ?? '',
					    'ContactPhone' => $array['PickupAddress']['ContactPhone'],
					    'ContactEmail' => $array['PickupAddress']['ContactEmail'] ?? '',
					    'HouseNumber' => $array['PickupAddress']['HouseNumber'] ?? '',
					    'HouseNumberInfo' => $array['PickupAddress']['HouseNumberInfo'] ?? '',
	         			'City' => $array['PickupAddress']['City'],
	         			'Street' => $array['PickupAddress']['Street'] ?? '-',
	         			'ZipCode' => $array['PickupAddress']['ZipCode'] ?? '',
	         			'CountryIsoCode' => $array['PickupAddress']['CountryIsoCode'] ?? $this->defaultCountryIso,

	   				],
					'DeliveryAddress' => [
						'Name' => $array['DeliveryAddress']['Name'],
	      				'ContactName' => $array['DeliveryAddress']['ContactName'] ?? '',
					    'ContactPhone' => $array['DeliveryAddress']['ContactPhone'],
					    'ContactEmail' => $array['DeliveryAddress']['ContactEmail'] ?? '',
					    'HouseNumber' => $array['DeliveryAddress']['HouseNumber'] ?? '',
					    'HouseNumberInfo' => $array['DeliveryAddress']['HouseNumberInfo'] ?? '',
	         			'City' => $array['DeliveryAddress']['City'],
	         			'Street' => $array['DeliveryAddress']['Street'] ?? '-',
	         			'ZipCode' => $array['DeliveryAddress']['ZipCode'] ?? '',
	         			'CountryIsoCode' => $array['DeliveryAddress']['CountryIsoCode'] ?? $this->defaultCountryIso,
	   				],
					'ServiceList' => array_merge(($array['public'] ? [
						[
					    	'Code' => 'PSS',
					    ]/*,[
					    	'Code' => 'PRS',
					    ]*/
					] : [
						// [
					    // 	'Code' => 'PSS',
					    // ],
					    /*[	// se introduce automat cand un client schimba data de ridicare din link-ul trimis la sosire colet (prin FDS, de intrebat)
					    	'Code' => 'DDS',
					    	'DDSParameter' => [
					    		// 'Value' => $array['pickupDate']
					    		'Value' => '/Date('.(strtotime('2024-03-19 08:30:00') * 1000).')/'
					    	],
					    ],*//*[
					    	'Code' => 'SDS',
					    	'SDSParameter' => [
					    		'TimeFrom' => $array['TimeFrom'],
								'TimeTo' => $array['TimeTo']
					    	],
					    ],*//*[
					    	'Code' => 'PSS',
					    ],*/[
					    	'Code' => 'FDS',
					    	'FDSParameter' => [
					    		'Value' => $array['PickupAddress']['ContactEmail'] ?? ''
					    	],
					    ]/*,[
					    	'Code' => 'FSS',
					    	'DDSParameter' => [
					    		'Value' => $array['PickupAddress']['ContactPhone'] ?? ''
					    	],
					    ]*/
					]), (isset($array['saturdayDelivery']) && $array['saturdayDelivery'] ? [
						['Code' => 'SAT']
					] : []), (isset($array['declaredValue']) && $array['declaredValue'] ? [
						[
							'Code' => 'DPV',
							'DPVParameter' => [
					    		'StringValue' => 'Valoarea declarata a pachetelor',
					    		'DecimalValue' => $array['declaredValue']
					    	],
						]
					] : []), (!empty($array['ramburs']) && $array['ramburs'] > 1 ? [
						['Code' => 'COD']
					] : []), (isset($array['rod']) && $array['rod'] ? [
						['Code' => 'SZL']
					] : []), (isset($array['swap']) && $array['swap'] ? [
						['Code' => 'XS']	// must be asked if it is a swap service
					] : [])),
				]],
			],
		];

		// \Log::info($data);
		// dd($data);

		$response = json_decode($this->connect($data), true);
		// $response = $this->connect($data);
		// print_r($response);
		// dd($response);
		// print_r($response['PrepareLabelsError']);
		// print_r($response['ParcelInfoList']); die();
		$pickup = isset($response['ParcelInfoList'][0]['ParcelId'])
			? $this->pickupOrder([
				'ParcelIdList' => collect($response['ParcelInfoList'])->pluck('ParcelId')->toArray(),
				'ParcelId' => $response['ParcelInfoList'][0]['ParcelId']
			]) 
			: false;
		if($pickup != false) {
			return [
				'id' => $response['ParcelInfoList'][0]['ParcelId'],
				'number' => $pickup[0]['ParcelNumber']
			];
		} else {
			Log::info('GLS Error Response');
			Log::info($response);
			Log::info($data['body']);
			return false;
		}
	}

	public function pickupOrder($array)
	{
		$requestURL = 'ParcelService.svc/json/GetPrintedLabels';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'Username' => $this->username,
				'Password' => $this->password,
				'ParcelIdList' => $array['ParcelIdList'],
				'PrintPosition ' => isset($array['PrintPosition']) && $array['PrintPosition'] >= 1 && $array['PrintPosition'] <= 4
					? $array['PrintPosition'] : 1,
				'ShowPrintDialog' => isset($array['ShowPrintDialog']) && $array['ShowPrintDialog'] === true ? 1 : 0,
				'TypeOfPrinter' => $array['TypeOfPrinter'] ?? 'Connect', // A4_2x2, A4_4x1, Connect, Thermo
			],
		];
		$response = json_decode($this->connect($data), true);

		if(empty($response['GetPrintedLabelsErrorList']) && isset($response['PrintDataInfoList'])) {
			if($awb = collect($response['PrintDataInfoList'])->pluck('ParcelNumber')->toArray()) {
				LivrareAwb::create([
					'api' => 3,
					'api_awb' => $array['ParcelId'],
					'parcel_list' => $array['ParcelIdList'],
					'parcel_awb_list' => $awb,
					'awb' => json_encode($response['Labels']),
				]);
			}
			return $response['PrintDataInfoList'];
		} else {
			Log::info('GLS Pickup Error Response');
			Log::info($response);
			Log::info($data['body']);
			return false;
		}
	}

	public function calculateOrder($array)
	{
		$response = [
			'total' => 0,
			'parcels' => [],
		];
		$min_kg = 2;
		$base_price = 16.07 + ($array['ramburs'] > 1 ? 3 : 0);
		$per_kg = 1.19;

		foreach($array['parcels'] ?? [] as $parcel) {
			if(!isset($parcel['weight'])) {
				return false;
			}
			if($parcel['weight'] <= $min_kg) {
				$parcel['total'] = $base_price;
			} else {
				$kgInPlus = ceil($parcel['weight'] - $min_kg) < 1 ? 1 : ceil($parcel['weight'] - $min_kg);
				$parcel['total'] = $base_price + ($kgInPlus * $per_kg);
			}
			$response['total'] += $parcel['total'];
			$response['parcels'][] = $parcel;
		}
		return count($response['parcels']) > 0 ? $response : false ;
	}

	// public function getOrder($array)
	// {
	// 	$requestURL = 'shipment/info';
	// 	$data = [
	// 		'url' => $this->baseURL.'/'.$requestURL,
	// 		'body' => [
	// 			'Username' => $this->username,
	// 			'Password' => $this->password,
	// 			'language' => $array['language'] ?? 'ro',
	// 			"shipmentIds" => [$array['shipmentId']],
	// 		],
	// 	];
	// 	$response = json_decode($this->connect($data), true)['shipments'];
	// 	return count($response) > 0 ? $response[0] : false ;
	// }

	public function getOrderParcels($array)
	{
		$requestURL = 'ParcelService.svc/json/GetParcelList';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'Username' => $this->username,
				'Password' => $this->password,
				'PickupDateFrom' => $array['PickupDateFrom'] ?? null,
				'PickupDateTo' => $array['PickupDateTo'] ?? null,
				'PrintDateFrom' => $array['PrintDateFrom'] ?? null,
				'PrintDateTo' => $array['PrintDateTo'] ?? null,
			],
		];
		$response = json_decode($this->connect($data), true);
		return isset($response['PrintDataInfoList']) && !empty($response['PrintDataInfoList']) 
			? $response['PrintDataInfoList'] : false;
	}

	public function trackParcels($array)
	{
		$requestURL = 'ParcelService.svc/json/GetParcelStatuses';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'Username' => $this->username,
				'Password' => $this->password,
				'ParcelNumber' => $array['ParcelNumber'],
				'ReturnPOD' => !empty($array['ReturnPOD']) ? true : false,
				'LanguageIsoCode' => $array['LanguageIsoCode'] ?? 'RO',
			],
		];
		$response = json_decode($this->connect($data), true);
		$response = isset($response['ParcelStatusList']) ? $response['ParcelStatusList'] : [];
		return count($response) > 0 ? $response : false ;
	}

	public function cancelOrder($array)
	{
		$requestURL = 'ParcelService.svc/json/DeleteLabels';
		$awb = LivrareAwb::where('api', 3)->where('api_awb', $array['awb'])->first();
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'Username' => $this->username,
				'Password' => $this->password,
				'ParcelIdList' => $awb ? $awb['parcel_list'] : [],
			],
		];
		$response = json_decode($this->connect($data), true);
		return empty($response['DeleteLabelsErrorList']) && $awb ? true : $response;
	}

	public function printAWB($array)
	{
		$awb = $this->getAWB($array);
		if($awb) {
			header('Content-Type: application/pdf');
			header('Content-Length: '.strlen($awb));
			header('Content-Disposition: attachment; filename="GLS_AWB_'.$array['awb'].'.pdf"');
			echo $awb; exit();
		} else {
			abort(404);
		}
	}

	public function getAWB($array)
	{
		$awb = LivrareAwb::where('api', 3)->where('api_awb', $array['awb'])->first();
		if($awb) {
			return implode(array_map('chr', json_decode($awb->awb, true)));
		} elseif(isset($array['return'])) {
			return false;
		} else {
			abort(404);
		}
	}

	public function formatDate($date, int $hours = 0)
	{
		$date = $date instanceof Carbon ? $date : Carbon::parse($date);
		return $date->timezone(config('app.timezone'))->startOfDay()->addHours($hours)->format('Y-m-d\TH:i:sO');
	}
}