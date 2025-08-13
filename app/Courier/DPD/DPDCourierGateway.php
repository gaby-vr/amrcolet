<?php 

namespace App\Courier\DPD;

use App\Courier\CourierGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DateTime;
use stdClass;
use SoapClient;
use SoapFault;
use Exception;

class DPDCourierGateway implements CourierGateway
{
	private $baseURL = 'https://api.dpd.ro/v1';
	private $shipmentURL = 'shipment';
	private $username = null;
	private $password = null;
	private $details = null;
	private $countryId = 642;

	public function __construct($array)
	{
		$this->username 				= $array['username'] ?? env('DPD_USERNAME_TEST');
		$this->password 				= $array['password'] ?? env('DPD_PASSWORD_TEST');
		if(isset($array['details'])) {	
			$this->details 				= $array['details'];
		}
	}

	public function setAll($array)
	{
		$this->username 				= $array['username'];
		$this->password 				= $array['password'];
		$this->details 					= $array['details'];
	}

	protected function connect($array)
	{
	    $ch = curl_init();
	    $headers = $array['headers'] ?? ['Content-Type: application/json'];

	    curl_setopt($ch, CURLOPT_URL, $array['url']);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    $body = json_encode($array['body']);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $array['method'] ?? "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    // Timeout in seconds
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

	    $output = curl_exec($ch);

	    curl_close($ch);

	    return $output;
	}

	public function setOrder($array)
	{
		$parcels = [];
		$i = 1;
		foreach ($array['parcels'] as $parcel) {
			$parcels[$i-1]['seqNo'] = $i;
			$parcels[$i-1]['size']['width'] = $parcel['width'];
			$parcels[$i-1]['size']['depth'] = $parcel['length'];
			$parcels[$i-1]['size']['height'] = $parcel['height'];
			$parcels[$i-1]['weight'] = $parcel['weight'];
			$i++;
		}
		$array['totalWeight'] = isset($array['totalWeight']) && $array['totalWeight'] > 1
			? $array['totalWeight']
			: 1;

		$data = [
			'url' => $this->baseURL.'/'.$this->shipmentURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				'sender' => [
				    'phone1' => [
				        'number' => $array['sender']['phone1'],
				    ],
				    'phone2' => [
				        'number' => $array['sender']['phone2'] ?? '',
				    ],
      				'privatePerson' => $array['sender']['privatePerson'] ?? false,
      				'clientName' => $array['sender']['clientName'],
      				'contactName' => $array['sender']['contactName'] ?? '',
      				'email' => $array['sender']['email'] ?? '',
      				'address' => [
         				'countryId' => $array['sender']['countryId'] ?? '',
         				'siteId' => $array['sender']['siteId'] ?? null,
         				'siteType' => $array['sender']['siteType'] ?? null,
         				'siteName' => $array['sender']['siteName'] ?? null,
         				'postCode' => $array['sender']['postCode'] ?? '',
         				'streetId' => $array['sender']['streetId'] ?? null,
         				'streetName' => $array['sender']['streetName'] ?? null,
         				'streetNo' => $array['sender']['streetNo'] ?? '',
         				'entranceNo' => $array['sender']['entranceNo'] ?? '',
         				'floorNo' => $array['sender']['floorNo'] ?? '',
         				'apartmentNo' => $array['sender']['apartmentNo'] ?? '',
         				'addressNote' => $array['sender']['addressNote'] ?? '',
         				'addressLine1' => $array['sender']['addressLine1'] ?? '',
         				'addressLine2' => $array['sender']['addressLine2'] ?? '',
      				]
   				],
				'recipient' => [
				    'phone1' => [
				        'number' => $array['recipient']['phone1'],
				    ],
				    'phone2' => [
				        'number' => $array['recipient']['phone2'] ?? '',
				    ],
      				'privatePerson' => $array['recipient']['privatePerson'] ?? false,
      				'clientName' => $array['recipient']['clientName'],
      				'contactName' => $array['recipient']['contactName'] ?? '',
      				'email' => $array['recipient']['email'] ?? '',
      				'address' => [
         				'countryId' => $array['recipient']['countryId'] ?? '',
         				'siteId' => $array['recipient']['siteId'] ?? null,
         				'siteType' => $array['recipient']['siteType'] ?? null,
         				'siteName' => $array['recipient']['siteName'] ?? null,
         				'postCode' => $array['recipient']['postCode'] ?? '',
         				'streetId' => $array['recipient']['streetId'] ?? null,
         				'streetName' => $array['recipient']['streetName'] ?? null,
         				'streetNo' => $array['recipient']['streetNo'] ?? '',
         				'entranceNo' => $array['recipient']['entranceNo'] ?? '',
         				'floorNo' => $array['recipient']['floorNo'] ?? '',
         				'apartmentNo' => $array['recipient']['apartmentNo'] ?? '',
         				'addressNote' => $array['recipient']['addressNote'] ?? '',
         				'addressLine1' => $array['recipient']['addressLine1'] ?? '',
         				'addressLine2' => $array['recipient']['addressLine2'] ?? '',
      				]
   				],
				'service' => [
				    'serviceId' => $array['serviceId'] ?? 2505,
				    'autoAdjustPickupDate' => true,
				    'pickupDate' => $array['pickupDate'],
				    'saturdayDelivery' => !empty($array['saturdayDelivery']) ? true : false,
				    'additionalServices' => [
				        'declaredValue' => [
				            'amount' => $array['declaredValue'] ?? '',
				         ],
				        'cod' => [
				        	"amount" => $array['rambursValue'] ?? 0,
    						"currencyCode" => $array['currencyCode'] ?? "RON",
   							"payoutToThirdParty" => true,
						    "processingType" => $array['ramburs'] == '2' ? "POSTAL_MONEY_TRANSFER" : "CASH",
						    "includeShippingPrice" => false
				        ],
				        'obpd' => [
				        	"option" => $array['obpd'] ? 'OPEN' : null,
    						"returnShipmentServiceId" => $array['serviceId'] ?? 2505,
   							"returnShipmentPayer" => 'THIRD_PARTY'
				        ],
				        // 'deliveryToFloor' => $array['deliveryToFloor'] ?? '',
				        'returns' => [
				            // 'rod' => [					// return of documents after the primary shipment is delivered
				            //    'enabled' => $array['rod'] ? true : false,
				            //    "thirdPartyPayer" => true, // who pays the return document shipment
				            // ],
				            'swap' => [					// return of parcels after the primary shipment is delivered
				               'serviceId' => $array['serviceId'] ?? 2505,
				               'parcelsCount' => $array['swap'] && $array['swap_parcels'] ? $array['swap_parcels'] : 0,
				               "thirdPartyPayer" => true, // who pays the return parcel shipment
				            ],
				        ],
				    ],
				],
				'content' => [
			    	'parcelsCount' => $array['parcelsCount'] > 0 ? $array['parcelsCount'] : 1,
			    	'totalWeight' => $array['totalWeight'] ?? 1,
			    	'contents' => $array['contents'],
			    	'parcels' => $parcels,
			    	'package' => count($parcels) > 0 ? 'cutie' : 'plic',
			   	],
   				'payment' => [
			      	'courierServicePayer' => 'THIRD_PARTY',
			      	'thirdPartyClientId' => 44117011000,
			      	// 'senderBankAccount' => [
			      	//  	'iban' => 'RO04BREL0002003101360100', // $array['iban'] ?? '',
         	//  			'accountHolder' => 'AMRCOLET SRL', // $array['accountHolder'] ?? '',
			      	// ],
			   	],
			   	'ref1' => $array['ref'],
			],
		];
		if($array['ramburs'] == '1') {
			unset($data['service']['additionalServices']['cod']);
			unset($data['payment']['senderBankAccount']);
		}
		if(!$array['swap']) {
			unset($data['service']['additionalServices']['returns']);	
		}

		// \Log::info($data);

		$response = json_decode($this->connect($data), true);
		
		$pickup = isset($response['id']) && isset($response['pickupDate'])
			? $this->pickupOrder([
				'shipmentId' => $response['id'], 
				// 'pickupDate' => $array['pickupDate'],
				'pickupDate' => $response['pickupDate'],
				'pickUpStartTime' => $array['pickUpStartTime'],
				'visitEndTime' => $array['visitEndTime'],
			]) 
			: false;
		if($pickup != false) {
			return $response['id'];
		} else {
			Log::info('DPD Error Response');
			Log::info($response);
			Log::info($data['body']);
			return false;
		}
	}

	public function pickupOrder($array)
	{
		$array['pickupDate'] = $array['pickupDate'] instanceof Carbon 
			? $array['pickupDate'] 
			: Carbon::parse($array['pickupDate']);
		if($array['pickupDate'] > now() || (int)$array['pickUpStartTime'] > (int)now()->format('H')) {
			$hours = (int)$array['pickUpStartTime'];
		} else {
			$hours = (int)now()->format('H') + 1;
		}

		$array['pickupDate']->startOfDay()->addHours($hours);

		$requestURL = 'pickup';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				'pickupDateTime' => str_replace(' ','T',$array['pickupDate']).'+0200',
				// 'pickupDateTime' => $array['pickupDate']->format('Y-m-d\TH:i:sO'),
				'explicitShipmentIdList' => [$array['shipmentId']],
				'visitEndTime' => sprintf("%02d", $array['visitEndTime']).':00',
			],
		];
		$response = $this->connect($data);

		if(isset(json_decode($response, true)['orders'])) {
			return true;
		} else {
			Log::info('DPD Pickup Error Response');
			Log::info($response);
			Log::info($data['body']);
			return false;
		}
	}

	public function calculateOrder($array)
	{
		$requestURL = 'calculate';
		$parcels = [];
		if(count($array['parcels']) > 0) {
			$i = 0;
			foreach ($array['parcels'] as $parcel) {
				$parcels[$i]['seqNo'] = $parcel['id'] ?? $i;
				$parcels[$i]['size']['width'] = $parcel['width'] ?? 10;
				$parcels[$i]['size']['depth'] = $parcel['length'] ?? 10;
				$parcels[$i]['size']['height'] = $parcel['height'] ?? 10;
				$parcels[$i]['weight'] = $parcel['weight'] ?? 1;
				$i++;
			}
		}
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				'sender' => [
      				'privatePerson' => $array['privatePerson'] ?? false,
      				'addressLocation' => [
         				'countryId' => $array['senderCountryId'] ?? '',
         				'siteId' => $array['senderSiteId'] ?? '',
         				'siteType' => $array['senderSiteType'] ?? '',
         				'siteName' => $array['senderSiteName'] ?? '',
         				'postCode' => $array['senderPostCode'] ?? '',
      				]
   				],
				'recipient' => [
      				'privatePerson' => $array['privatePerson'] ?? false,
      				'addressLocation' => [
         				'countryId' => $array['receiverCountryId'] ?? '',
         				'siteId' => $array['receiverSiteId'] ?? '',
         				'siteType' => $array['receiverSiteType'] ?? '',
         				'siteName' => $array['receiverSiteName'] ?? '',
         				'postCode' => $array['receiverPostCode'] ?? '',
      				]
   				],
				'service' => [
				    'serviceIds' => [$array['serviceId'] ?? 2505],
				    'autoAdjustPickupDate' => true,
				    'pickupDate' => $array['pickupDate'] ?? now()->format('Y-m-d'),
				    'saturdayDelivery' => !empty($array['saturdayDelivery']) ? true : false,
				    'additionalServices' => [
				        'declaredValue' => [
				            'amount' => $array['declaredValue'] ?? null,
				         ],
				        'cod' => [
				        	"amount" => $array['rambursValue'] ?? 0,
    						"currencyCode" => $array['currencyCode'] ?? "RON",
   							"payoutToThirdParty" => true,
						    "processingType" => $array['ramburs'] == '2' ? "POSTAL_MONEY_TRANSFER" : "CASH",
						    "includeShippingPrice" => false
				        ],
				        'obpd' => [
				        	"option" => !empty($array['obpd']) ? 'OPEN' : null,
    						"returnShipmentServiceId" => $array['serviceId'] ?? 2505,
   							"returnShipmentPayer" => 'THIRD_PARTY'
				        ],
				        // 'deliveryToFloor' => $array['deliveryToFloor'] ?? '',
				        'returns' => [
				            // 'rod' => [	// return of documents after the primary shipment is delivered
				            //    'enabled' => isset($array['rod']) && $array['rod'] ? true : false,
				            //    "thirdPartyPayer" => true, // who pays the return document shipment
				            // ],
				            'swap' => [					// return of parcels after the primary shipment is delivered
				               'serviceId' => $array['serviceId'] ?? 2505,
				               'parcelsCount' => !empty($array['swap']) && !empty($array['swap_parcels']) ? $array['swap_parcels'] : 0,
				               "thirdPartyPayer" => true, // who pays the return parcel shipment
				            ],
				        ],
				    ],
				],
				'content' => [
			    	'parcelsCount' => !empty($array['parcelsCount']) && $array['parcelsCount'] > 0 ? $array['parcelsCount'] : 1,
			    	'totalWeight' => $array['totalWeight'] ?? 1,
			    	'contents' => $array['contents'] ?? 'General',
			    	'parcels' => $parcels,
			    	'package' => count($parcels) > 0 ? 'cutie' : 'plic',
			   	],
   				'payment' => [
			      	'courierServicePayer' => 'THIRD_PARTY',
			      	'thirdPartyClientId' => 44117011000,
			      	// 'senderBankAccount' => [
			      	//  	'iban' => 'RO04BREL0002003101360100', // $array['iban'] ?? '',
         	//  			'accountHolder' => 'AMRCOLET SRL', // $array['accountHolder'] ?? '',
			      	// ],
			   	],
			],
		];
		if($array['ramburs'] == '1') {
			unset($data['body']['service']['additionalServices']['cod']);
			unset($data['body']['payment']['senderBankAccount']);
		}
		$response = json_decode($this->connect($data), true);
		$response = isset($response['calculations']) ? $response['calculations'] : [];
		return count($response) > 0 ? $response[0] : false ;
	}

	public function getServiceCode($countryIso, $receiver = true)
	{
		$countryIso = strtoupper($countryIso);
		if(in_array($countryIso, ['BG','GR','HU','PL','SK','SI','CZ','HR'])) {
			return 2212;
		} elseif($countryIso != 'RO') {
			return $receiver ? 2303 : 2323;
		}
		return 2505;
	}

	public function getOrder($array)
	{
		$requestURL = 'shipment/info';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				"shipmentIds" => [$array['shipmentId']],
			],
		];
		$response = json_decode($this->connect($data), true)['shipments'];
		return count($response) > 0 ? $response[0] : false ;
	}

	public function getOrderParcels($array)
	{
		$requestURL = 'shipment/info';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				'shipmentIds' => [$array['shipmentId']],
			],
		];
		$response = json_decode($this->connect($data), true);
		$response = isset($response['shipments']) ? $response['shipments'] : [];
		return count($response) > 0 ? $response[0]['content']['parcels'] : false ;
	}

	public function trackParcels($array)
	{
		$requestURL = 'track';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				'parcels' => $array['parcels'],
				'lastOperationOnly' => $array['lastOperationOnly'] ?? true,
			],
		];
		$response = json_decode($this->connect($data), true);
		$response = isset($response['parcels']) ? $response['parcels'] : [];
		return count($response) > 0 ? $response : false ;
	}

	public function cancelOrder($array)
	{
		$requestURL = 'shipment/cancel';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => $array['language'] ?? 'ro',
				'shipmentId' => $array['shipmentId'],
				'comment' => 'Cancel shipment',
			],
		];
		$response = json_decode($this->connect($data), true);
		return is_countable($response) && count($response) > 0 ? $response : true ;
	}

	public function printAWB($array)
	{
		$requestURL = 'print';
		$parcels = [];
		if(isset($array['parcels']) && is_countable($array['parcels'])) {
			foreach ($array['parcels'] as $parcel) {
				$parcels[]['parcel']['id'] = $parcel['id'];
			}
		}
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				"format" => $array['format'] ?? "pdf",
				"paperSize" => $array['paperSize'] == "" || $array['paperSize'] == null 
					? "A4" 
					: $array['paperSize'],
			    "parcels" => $parcels,
			],
		];

		$awb = $this->connect($data);
		header('Content-Type: application/pdf');
		header('Content-Length: '.strlen($awb));
		header('Content-Disposition: attachment; filename="DPD_AWB_'.$array['awb'].'.pdf"');
		echo $awb; exit();
	}

	public function getAWB($array)
	{
		$requestURL = 'print';
		$parcels = [];
		if($array['parcels'] != false) {
			foreach ($array['parcels'] as $parcel) {
				$parcels[]['parcel']['id'] = $parcel['id'];
			}
		}
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				"format" => $array['format'] ?? "pdf",
				"paperSize" => $array['paperSize'] ?? "A4",
			    "parcels" => $parcels,
			],
		];
		return $this->connect($data);
	}

	public function getPayments($from, $to)
	{
		$requestURL = 'payments';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'clientSystemId' => $array['clientSystemId'] ?? '',
				'fromDate' => $this->formatDate($from),
				'toDate' => $this->formatDate($to),
				'includeDetails' => true,
			],
		];
		$response = json_decode($this->connect($data), true);
		$response = isset($response['payouts']) ? $response['payouts'] : [];
		return count($response) > 0 ? $response : false;
	}

	public function findCountry($array)
	{
		$requestURL = 'location/country';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'isoAlpha2' => $array['isoAlpha2'],
			],
		];
		$response = json_decode($this->connect($data), true);
		if(isset($response['countries'])) {
			$response = $response['countries'];
		} else {
			\Log::info($response);
			$response = [];
		}
		return count($response) > 0 ? $response[0] : false ;
	}

	public function findState($array)
	{
		$requestURL = 'location/state';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'countryId' => $array['countryId'],
				'name' => $array['name'],
			],
		];
		$response = json_decode($this->connect($data), true)['states'];
		return count($response) > 0 ? $response[0] : false ;
	}

	public function getAllStatesInCSVFile($array)
	{
		$requestURL = 'location/state/csv/'.$array['countryId'];
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
			],
		];
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="states.csv"');
		echo $this->connect($data); exit();
	}

	public function getAllPostcodesInCSVFile($array = [])
	{
		$requestURL = 'location/postcode/csv/'.($array['countryId'] ?? $this->countryId);
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
			],
		];
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="postcodes.csv"');
		echo $this->connect($data); exit();
	}

	// region = county
	public function findSite($array)
	{
		$requestURL = 'location/site';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'countryId' => $array['countryId'] ?? $this->countryId,
				'name' => $array['name'] ?? '',
				'postCode' => $array['postCode'] ?? '',
				'type' => $array['type'] ?? '',
				'region' => $array['region'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['sites'] ?? [];
		if(is_array($response) && count($response) < 1) {
			$data['body']['region'] = str_replace('-', ' ', $data['body']['region']);
			$response = json_decode($this->connect($data), true)['sites'] ?? [];
		}
		if(is_array($response) && count($response) < 1) {
			$data['body']['region'] = str_replace(' ', '-', $data['body']['region']);
			$response = json_decode($this->connect($data), true)['sites'] ?? [];
		} elseif(!is_array($response)) {
			Log::info($response);
		}
		
		return is_array($response) && count($response) > 0 ? $response[0] : false ;
	}

	public function findStreet($array, $first = true)
	{
		$requestURL = 'location/street';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'siteId' => $array['siteId'],
				'name' => $array['name'] ?? '',
				'type' => $array['type'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['streets'] ?? [];
		return count($response) > 0 ? ($first ? $response[0] : $response) : false ;
	}

	public function getAllStreetsInCSVFile($array = [])
	{
		$requestURL = 'location/street/csv/'.($array['countryId'] ?? $this->countryId);
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
			],
		];
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="streets.csv"');
		echo $this->connect($data); exit();
	}

	public function findComplex($array)
	{
		$requestURL = 'location/complex';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'siteId' => $array['siteId'],
				'name' => $array['name'] ?? '',
				'type' => $array['type'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['complexes'];
		return count($response) > 0 ? $response[0] : false ;
	}

	public function findBlock($array)
	{
		$requestURL = 'location/block';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'siteId' => $array['siteId'],
				'name' => $array['name'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['blocks'];
		return count($response) > 0 ? $response[0] : false ;
	}

	public function findOffice($array)
	{
		$requestURL = 'location/office';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'countryId' => $array['countryId'] ?? '',
				'siteId' => $array['siteId'] ?? '',
				'name' => $array['name'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['offices'];
		return count($response) > 0 ? $response[0] : false ;
	}

	public function services()
	{
		$requestURL = 'services';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'clientSystemId' => $array['clientSystemId'] ?? '',
				'date' => $array['date'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['services'];
		return count($response) > 0 ? $response : false ;
	}

	public function getClient()
	{
		$requestURL = 'client/contract';
		$data = [
			'url' => $this->baseURL.'/'. $requestURL,
			'body' => [
				'userName' => $this->username,
				'password' => $this->password,
				'language' => 'ro',
				'clientSystemId' => $array['clientSystemId'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true)['clients'];
		return count($response) > 0 ? $response : false ;
	}

	public function formatDate($date, int $hours = 0)
	{
		$date = $date instanceof Carbon ? $date : Carbon::parse($date);
		return $date->timezone(config('app.timezone'))->startOfDay()->addHours($hours)->format('Y-m-d\TH:i:sO');
	}
}