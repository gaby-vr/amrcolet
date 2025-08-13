<?php 

namespace App\Courier\PostisGate;

use App\Courier\CourierGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use DateTime;
// use stdClass;
// use SoapClient;
// use SoapFault;
// use Exception;

class PostisGateCourierGateway implements CourierGateway
{
	/*
	 *	Important! 
	 *	
	 *	For 'shipmentPayment' always select 'SENDER' and manualy add an 'senderLocation' for 'THIRD_PARTY' option to be available 
	 *	'shipmentPayment' possible values: "SENDER", "RECEIVER"
	 *	'sendType' possible values: "FORWARD", "BACK", "BACK14", "REPAIR", "FORWARD_AND_BACK" "REPLENISHMENT", "TRANSFER", "COST_PER_COURIER_QUOTE", 
	 *								"COST_PER_COURIER_QUOTE_AND_FORWARD", "GENERIC"
	*/

	private $baseURL = 'https://shipments.postisgate.com';
	private $username = null;
	private $password = null;
	private $token = null;
	private $details = null;
	private $sandbox = true;
	private $clientId = null;
	private $countryId = 642;
	private $currency = 'RON';
	private $couriers = [1 => 'CARGUS', 2 => 'DPD'];

	public function __construct($array)
	{
		$this->username = $array['username'] ?? ($this->sandbox ? env('POSTIS_USERNAME_TEST') : env('POSTIS_USERNAME'));
		$this->password = $array['password'] ?? ($this->sandbox ? env('POSTIS_PASSWORD_TEST') : env('POSTIS_PASSWORD'));
		$this->clientId = $array['client_id'] ?? ($this->sandbox ? env('POSTIS_CLIENT_ID_TEST') : env('POSTIS_CLIENT_ID'));
		if(isset($array['details'])) {	
			$this->details = $array['details'];
		}
	}

	public function setAll($array)
	{
		$this->username = $array['username'];
		$this->password = $array['password'];
		$this->details 	= $array['details'];
	}

	protected function getToken()
	{
		$requestURL = 'unauthenticated/login';

		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'token' => true,
			'body' => [
				'name' => $this->username,
				'password' => $this->password,
			],
		];
		$token = $this->token ?? json_decode($this->connect($data), true);
		$this->token = is_array($token) && isset($token['token']) ? $token['token'] : null;
		// Log if the token was not received
		!$this->token && is_array($token) ? \Log::info($token) : null;
		return $this->token;
	}

	protected function connect($array)
	{
	    $ch = curl_init();
	    $headers = $array['headers'] ?? ['Content-Type: application/json'];
	    $body = json_encode($array['body']);

	    $headers = [
    		2 => 'Content-Type: application/json', 
    		3 => 'Content-Length: '.strlen($body),
    	] + (isset($array['token']) ? [] : [
    		4 => 'Authorization: Bearer '.$this->getToken(),
    	]) + $headers;

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
		$err = curl_error($ch);

	    curl_close($ch);

	    if ($err) {
		  	\Log::info("cURL Error #:" . $err);
		}

	    return $output;
	}

	public function setOrder($array)
	{
		$requestURL = 'api/v1/clients/shipments';

		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'body' => [
				'clientId' => $this->clientId,
				'clientOrderId' => $array['id'],
				'clientOrderDate' => Carbon::parse($array['created_at'])->format('Y-m-d H:m:i'),
				'productCategory' => 'Standard Delivery',
				'sendType' => 'FORWARD',
				'shipmentPayer' => 'SENDER',
				'shipmentReference' => $array['id'],
				'sourceChannel' => 'ONLINE',
				'senderLocation' => $this->prepareAddress($array['sender']),
				'recipientLocation' => $this->prepareAddress($array['receiver'], 2),
				'shipmentParcels' => $this->prepareParcels($array), 		// pracels object
				'senderReference' => $livrare['customer_reference'] ?? '',	// optional
				'additionalServices' => [									// optional
					'cashOnDelivery' => $array['ramburs_value'] ?? 0,
					// 'cashOnDeliveryReference' => '',
					// 'IBAN' => '',
					'insurance' =>  false, // $array['assurance'] ? true : false,				// boolean
					'openPackage' => $array['open_when_received'] ? true : false,				// boolean
					'retourDoc' => $array['retur_document'] ? true : false,						// boolean
					'saturdayDelivery' => $array['work_saturday'] ? true : false,				// boolean
					// 'morningDelivery' => '',													// boolean
					// 'priorityDelivery' => '',												// boolean
				],

				'courierId' => $this->getCourierId($array['api']),  		// optional
				'pickupDate' => $array['pickup_day'], 						// optional
			],
		];
		$response = json_decode($this->connect($data), true);
		if(isset($response['shipmentId'])) {
			return ['status' => 200, 'awb' => $response['shipmentId']];
		}
		\Log::info('Postis request failed'); 
		\Log::info('Response:'); 
		\Log::info($response); 
		\Log::info('Data:');
		\Log::info($data);

		return ['status' => 500];
	}

	/*public function calculateOrder($array)
	{
		$requestURL = 'calculate';
		
		$response = json_decode($this->connect($data), true)['calculations'];
		return count($response) > 0 ? $response[0] : false ;
	}*/

	public function getCourierId($id)
	{
		return $this->couriers[$id];
	}

	public function prepareAddress($address, $type = 1)
	{
		$addressText =
            (isset($address['street']) && $address['street'] != '-' ? 'Strada '.$address['street'] : '').
            (isset($address['street_nr']) && $address['street_nr'] != '-' ? ' Nr. '.$address['street_nr'] : '').
            (isset($address['bl_code']) ? ', Bl. '.$address['bl_code'] : '').
            (isset($address['bl_letter']) ? ', Sc. '.$address['bl_letter'] : '').
            (isset($address['intercom']) ? ', Interfon '.$address['intercom'] : '').
            (isset($address['floor']) ? ', Etaj '.$address['floor'] : '').
            (isset($address['apartment']) ? ', Ap./Nr. '.$address['apartment'] : '');

		return [
			'addressText' => $addressText,
			'contactPerson' => $address['name'],
			'country' => explode(' (', $address['country'])[0],
			'county' => $address['county'],
			'locality' => $address['locality'],
			'locationId' => $type == 1 ? 'AmrcoletSender#ID' : 'AmrcoletReceiver#ID',
			'name' => $address['company'] ?? $address['name'],
			'phoneNumber' => $address['phone'],
			'postalCode' => $address['postcode'],
			'buildingNumber' => $address['bl_code'] ?: null ,	// optional
			'email' => $address['email'],						// optional
		];
	}

	public function prepareParcels($array)
	{
		$parcels = [];
		$i = 0;
		if($array['type'] == '1') {
			foreach ($array['parcels'] as $parcel) {
				$parcels[$i] = [
					'itemCode' => $parcel['id'],
					'itemDescription1' => __('Comanda #:order pachet :nr', [
						'order' => $array['id'], 
						'nr' => $i + 1
					]),
					'itemUOMCode' => 'Buc.',
					'parcelDeclaredValue' => $array['assurance'] ?? 0,
					'parcelReferenceId' => $array['id'].'#'.$parcel['id'],
					'parcelType' => 'PACKAGE',

					'parcelContent' => $array['content'],	// optional
					'parcelWidth' => $parcel['width'],  	// optional
					'parcelLength' => $parcel['length'],  	// optional
					'parcelHeight' => $parcel['height'],  	// optional
					'parcelBrutWeight' => $parcel['weight'],
				];
				$i++;
			}
		} else {
			$parcels[$i] = [
				'itemCode' => $array['id'].'_1',
				'itemDescription1' => __('Comanda #:order plic :nr', [
					'order' => $array['id'], 
					'nr' => $i + 1
				]),
				'itemUOMCode' => 'Buc.',
				'parcelDeclaredValue' => $array['assurance'] ?? 0,
				'parcelReferenceId' => $array['id'].'#1',
				'parcelType' => 'ENVELOPE',
				'parcelContent' => $array['content'], // optional
				'parcelBrutWeight' => 1,
			];
		}
		return $parcels;
	}


	public function trackOrder($array)
	{
		$requestURL = 'api/v1/clients/shipments/trace';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [
				'awblist' => $array['awbs'],
				'type' => $array['type'] ?? 'last', 	// 'history' or 'last'
			],
		];
		$response = json_decode($this->connect($data), true);
		return count($response) > 0 ? $response : false ;
	}

	public function cancelOrder($awb)
	{
		$requestURL = 'api/v1/clients/shipments/'.$awb;
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'DELETE',
			'body' => [],
		];
		$response = json_decode($this->connect($data), true);
		return count($response) > 0 ? $response : true ;
	}

	public function printAWB($awb)
	{
		$requestURL = 'api/v1/clients/shipments/'.$awb.'/label';
		$data = [
			'url' => $this->baseURL.'/'.$requestURL,
			'method' => 'GET',
			'body' => [],
		];

		$awb = $this->connect($data);
		header('Content-Type: application/pdf');
		header('Content-Length: '.strlen($awb));
		header('Content-Disposition: attachment; filename="AWB_'.$array['awb'].'.pdf"');
		echo $awb; exit();
	}

}