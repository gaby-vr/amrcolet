<?php 

namespace App\Billing\Mobilpay;

use DateTime;
use stdClass;
use SoapClient;
use SoapFault;
use Exception;
use App\Billing\PaymentGateway;
use App\Billing\Mobilpay\Mobilpay_Payment_Address;
use App\Billing\Mobilpay\Mobilpay_Payment_Invoice;
use App\Billing\Mobilpay\Mobilpay_Payment_Request_Abstract;
use App\Billing\Mobilpay\Mobilpay_Payment_Request_Card;
use App\Billing\Mobilpay\Mobilpay_Payment_Request_Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobilpayPaymentGateway implements PaymentGateway
{
	private $currency = 'RON';
	private $amount = null;
	private $type = null;
	private $firstName = null;
	private $lastName = null;
	private $email = null;
	private $phone = null;
	private $address = null;
	private $returnURL = null;
	private $confirmURL = null;
	private $sandbox = false;
	private $signature = null;
	private $livePublicFilePath = null;
	private $sandboxPublicFilePath = null;
	private $livePrivateFilePath = null;
	private $sandboxPrivateFilePath = null;
	private $params = null;
	private $details = null;
	private $observerUsername = null;
	private $observerPassword = null;
	private $mobilpayUsername = null;
	private $mobilpayPassword = null;
	private $orderId = null;
	private $recurrenceTokenId = null;

	public function __construct($array)
	{
		$this->livePublicFilePath = resource_path(
			env('MOBILPAY_CERTIFICATE_PATH')
			.env('MOBILPAY_LIVE_PUBLIC_CERTIFICATE')
		) ?? null;
		$this->sandboxPublicFilePath = resource_path(
			env('MOBILPAY_CERTIFICATE_PATH')
			.env('MOBILPAY_SANDBOX_PUBLIC_CERTIFICATE')
		) ?? null;
		$this->livePrivateFilePath = resource_path(
			env('MOBILPAY_CERTIFICATE_PATH')
			.env('MOBILPAY_LIVE_PRIVATE_CERTIFICATE')
		) ?? null;
		$this->sandboxPrivateFilePath = resource_path(
			env('MOBILPAY_CERTIFICATE_PATH')
			.env('MOBILPAY_SANDBOX_PRIVATE_CERTIFICATE')
		) ?? null;

		$this->signature 		= env('MOBILPAY_SELLER_SIGNATURE') ?? null;
		$this->observerUsername = env('MOBILPAY_OBSERVER_USERNAME') ?? null;
		$this->observerPassword = env('MOBILPAY_OBSERVER_PASSWORD') ?? null;
		$this->mobilpayUsername = env('MOBILPAY_USERNAME') ?? null;
		$this->mobilpayPassword = env('MOBILPAY_PASSWORD') ?? null;

		foreach($array as $key => $value) {
			$this->{$key} = $value;
		}

		if(auth()->id() == 1) {
			$this->sandbox = true;
		}
	}

	public function setAll($array)
	{
		$this->returnURL 				= $array['returnURL'];
		$this->confirmURL 				= $array['confirmURL'];
		$this->currency 				= $array['currency'];
		$this->amount 					= $array['amount'];
		$this->signature 				= $array['signature'];
		$this->firstName 				= $array['firstName'];
		$this->lastName 				= $array['lastName'];
		$this->email 					= $array['email'];
		$this->phone 					= $array['phone'];
		$this->address 					= $array['address'];
		$this->params 					= $array['params'];
		$this->sandbox 					= $array['sandbox'];
		$this->type 					= $array['type'];
		$this->details 					= $array['details'];
		$this->livePublicFilePath 		= $array['livePublicFilePath'];
		$this->sandboxPublicFilePath 	= $array['sandboxPublicFilePath'];
		$this->livePrivateFilePath 		= $array['livePrivateFilePath'];
		$this->sandboxPrivateFilePath 	= $array['sandboxPrivateFilePath'];
		$this->observerUsername 		= $array['observerUsername'];
		$this->observerPassword 		= $array['observerPassword'];
		$this->mobilpayUsername 		= $array['mobilpayUsername'];
		$this->mobilpayPassword 		= $array['mobilpayPassword'];
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}

	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	public function setAddress($address)
	{
		$this->address = $address;
	}

	public function setReturnURL($url)
	{
		$this->returnURL = $url;
	}

	public function setConfirmURL($url)
	{
		$this->confirmURL = $url;
	}

	public function setDetails($string)
	{
		$this->details = $string;
	}

	public function setUsername($string)
	{
		$this->mobilpayUsername = $string;
	}

	public function setPassword($string)
	{
		$this->mobilpayPassword = $string;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function generateOrderId()
	{
		srand((double) microtime() * 1000000);
		return $this->orderId = md5(uniqid(rand()));
	}

	public function setForm()
    {
    	if($this->sandbox) {
	    	$paymentUrl 	= 'https://sandboxsecure.mobilpay.ro';
	    	$x509FilePath 	= $this->sandboxPublicFilePath;
	    } else {
			$paymentUrl 	= 'https://secure.mobilpay.ro';
	   		$x509FilePath 	= $this->livePublicFilePath;
	   	}

    	try {
			if($this->orderId == null) {
				srand((double) microtime() * 1000000);
				$this->orderId					= md5(uniqid(rand()));
			}

			$objPmReqCard 						= new Mobilpay_Payment_Request_Card();
			$objPmReqCard->signature 			= $this->signature;
			$objPmReqCard->orderId 				= $this->orderId;
			$objPmReqCard->confirmUrl 			= $this->confirmURL; 
			$objPmReqCard->returnUrl 			= $this->returnURL;
			$objPmReqCard->params 				= $this->params;
			$objPmReqCard->invoice 				= new Mobilpay_Payment_Invoice();
			$objPmReqCard->invoice->currency	= $this->currency ?? 'RON';
			$objPmReqCard->invoice->amount		= $this->amount;
			// $objPmReqCard->invoice->installments= '2,3';
			// $objPmReqCard->invoice->selectedInstallments= '3';
		    // $objPmReqCard->invoice->tokenId 	= $this->recurrenceTokenId // 'token_id';
		    if($this->details != null) {
				$objPmReqCard->invoice->details	= $this->details;
		    }
			# detalii cu privire la adresa posesorului cardului
			# details on the cardholder address (optional)
			$billingAddress 				= new Mobilpay_Payment_Address();
			$billingAddress->type			= $this->type;	// 'company' or 'person'
			$billingAddress->firstName		= $this->firstName;
			$billingAddress->lastName		= $this->lastName;
			$billingAddress->email			= $this->email;
			$billingAddress->mobilePhone	= $this->phone;
			$billingAddress->address		= $this->address;
			$objPmReqCard->invoice->setBillingAddress($billingAddress);
			$objPmReqCard->encrypt($x509FilePath);
		} catch(Exception $e) { 
			Log::info(json_encode($e->getMessage())); 
		}

        $data = [
		    'env_key' => $objPmReqCard->getEnvKey(),
		    'data' => $objPmReqCard->getEncData(),
		    'url' => $paymentUrl,
		];

		$form = '<form action="' . $data['url'] . '" name="frmPaymentRedirect" method="post" style="display:none;">';
		$form .= '<input type="hidden" name="env_key" value="' . $data['env_key'] . '" >';
		$form .= '<input type="hidden" name="data" value="' . $data['data'] . '" >';
		$form .= '<input type="submit" id="submit_mobilpay_payment_form" value="' . __('Submit') . '" ></form>';
		$form .= '<script>document.getElementById("submit_mobilpay_payment_form").click();</script>';

		return $form;
    }

    public function confirm()
    {
    	$data			= [];	
        $errorCode 		= 0;
		$errorType		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_NONE;
		$errorMessage	= '';
		if(strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0)
		{
			if(isset($_POST['env_key']) && isset($_POST['data']))
			{
				if($this->sandbox)
		    	{
			    	$privateKeyFilePath = $this->sandboxPrivateFilePath;
			    }
			    else
			    {
			   		$privateKeyFilePath = $this->livePrivateFilePath;
			   	}
				try
				{
					$objPmReq = Mobilpay_Payment_Request_Abstract::factoryFromEncrypted($_POST['env_key'], $_POST['data'], $privateKeyFilePath);
					$rrn = $objPmReq->objPmNotify->rrn;

					$data['params'] = $objPmReq->params;
					if($objPmReq->objPmNotify)
    				{
						$data['purchaseId'] 	= $objPmReq->objPmNotify->purchaseId;
						$data['originalAmount'] = $objPmReq->objPmNotify->originalAmount;
					}
					if ($objPmReq->objPmNotify->errorCode == 0) {
					    switch($objPmReq->objPmNotify->action)
					    {
						case 'confirmed':
								#cand action este confirmed avem certitudinea ca banii au plecat din contul posesorului de card si facem update al starii comenzii si livrarea produsului
							$data['status'] = $objPmReq->objPmNotify->action;
							$data['orderId'] = $objPmReq->orderId;
							if(isset($objPmReq->objPmNotify->token_id)) {
								$data['tokenId'] = $objPmReq->objPmNotify->token_id;
							}
							$errorMessage = $objPmReq->objPmNotify->errorMessage;
						    break;
						case 'confirmed_pending':
							#cand action este confirmed_pending inseamna ca tranzactia este in curs de verificare antifrauda. Nu facem livrare/expediere. In urma trecerii de aceasta verificare se va primi o noua notificare pentru o actiune de confirmare sau anulare.
							$data['status'] = $objPmReq->objPmNotify->action;
							$errorMessage = $objPmReq->objPmNotify->errorMessage;
						    break;
						case 'paid_pending':
							#cand action este paid_pending inseamna ca tranzactia este in curs de verificare. Nu facem livrare/expediere. In urma trecerii de aceasta verificare se va primi o noua notificare pentru o actiune de confirmare sau anulare.
							$data['status'] = $objPmReq->objPmNotify->action;
							$errorMessage = $objPmReq->objPmNotify->errorMessage;
						    break;
						case 'paid':
							#cand action este paid inseamna ca tranzactia este in curs de procesare. Nu facem livrare/expediere. In urma trecerii de aceasta procesare se va primi o noua notificare pentru o actiune de confirmare sau anulare.
							$data['status'] = $objPmReq->objPmNotify->action;
							$errorMessage = $objPmReq->objPmNotify->errorMessage;
						    break;
						case 'canceled':
							#cand action este canceled inseamna ca tranzactia este anulata. Nu facem livrare/expediere.
							$data['status'] = $objPmReq->objPmNotify->action;
							$errorMessage = $objPmReq->objPmNotify->errorMessage;
						    break;
						case 'credit':
							#cand action este credit inseamna ca banii sunt returnati posesorului de card. Daca s-a facut deja livrare, aceasta trebuie oprita sau facut un reverse.
							$data['status'] = $objPmReq->objPmNotify->action;
							$errorMessage = $objPmReq->objPmNotify->errorMessage;
						    break;
						default:
							$data['default'] = 'default';
							$errorType		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
						    $errorCode 		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_ACTION;
						    $errorMessage 	= 'mobilpay_refference_action paramaters is invalid';
						    break;
					    }
					}
					else 
					{
						$data['status'] = 'rejected';
						$errorMessage = $objPmReq->objPmNotify->errorMessage;
						Log::info(json_encode($errorMessage));
					}
				}
				catch(Exception $e)
				{
					$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_TEMPORARY;
					$errorCode		= $e->getCode();
					$errorMessage 	= $e->getMessage();
					Log::info(json_encode($e->getMessage()));
				}
			}
			else
			{
				$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
				$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
				$errorMessage 	= 'mobilpay.ro posted invalid parameters';
				Log::info(json_encode($errorMessage));
			}
		}
		else 
		{
			$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
			$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_METHOD;
			$errorMessage 	= 'invalid request metod for payment confirmation';
			Log::info(json_encode($errorMessage));
		}
		header('Content-type: application/xml');
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		if($errorCode == 0)
		{
			echo "<crc>{$errorMessage}</crc>";
		}
		else
		{
			echo "<crc error_type=\"{$errorType}\" error_code=\"{$errorCode}\">{$errorMessage}</crc>";
		}
		return $data;
    }

    public function recurrence($token_id = null)
    {
    	ini_set('display_errors',1);
		define("ERR_CODE_OK",0x00);

		$paymentUrl = $this->sandbox 
    		? 'https://sandboxsecure.mobilpay.ro'
    		: 'https://secure.mobilpay.ro';
		   	
		$soap = new SoapClient($paymentUrl.'/api/payment2/?wsdl', Array('cache_wsdl' => WSDL_CACHE_NONE));

		$sacId = $this->signature;

		$req = new stdClass();

	    $account = new stdClass();
	    $account->id = $sacId;
	    $account->user_name = $this->observerUsername; // observer admin account username
	    $account->customer_ip = $_SERVER['REMOTE_ADDR'] ?? ''; // the IP address of the buyer. 
	    $account->confirm_url = $this->confirmURL;

	    $transaction = new stdClass();
	    $transaction->paymentToken = $token_id ?? $this->recurrenceTokenId; // you will receive this token together with its expiration date following a standard payment. Please store and use this token with maximum care

	    $billing = new stdClass();
	    $billing->type			= $this->type;
		$billing->first_name	= $this->firstName;
		$billing->last_name		= $this->lastName;
		$billing->email			= $this->email;
		$billing->phone			= $this->phone;
		$billing->address		= $this->address;
		// $billing->country	 = 'billing_country';
		// $billing->county 	 = 'billing_county';
		// $billing->city 		 = 'billing_city';
		// $billing->postal_code = 'billing_postal_code';

	    if($this->orderId == null) {
			srand((double) microtime() * 1000000);
			$this->orderId = md5(uniqid(rand()));
		}
	    $order 				= new stdClass();
	    $order->id 			= $this->orderId; //your orderId. As with all mobilPay payments, it needs to be unique at seller account level
	    $order->description = $this->details; //payment descriptor
	    $order->amount 		= $this->amount; // order amount; decimals present only when necessary, i.e. 15 not 15.00
	    $order->currency 	= $this->currency ?? 'RON'; //currency
	    $order->billing 	= $billing;

	    $params = new stdClass();
	    $params->item = new stdClass();
	    if($this->params) {
	    	foreach($this->params as $key => $value) {
				$params->item->{$key} = $value;
	    	}
		}

	    $account->hash = strtoupper(sha1(strtoupper(md5($this->observerPassword))."{$order->id}{$order->amount}{$order->currency}{$account->id}"));

	    $req->account = $account;
	    $req->order = $order;
		$req->params = $params;
	    $req->transaction = $transaction;

	    try {
	        $response = $soap->doPayT(Array('request' => $req));
	        if (isset($response->errors) && $response->errors->code != ERR_CODE_OK)
	        {
	        	return $this->response($response, $response->code);
	            // throw new Exception($response->code, $response->message);
	        }
	        return $this->response($response);
	    } catch(SoapFault $e) {
			return $this->response($e, $e->faultcode, $e->faultstring);
	        // throw new Exception((int) $e->faultcode, (string)$e->faultstring, $e);
	    }
    }

    public function cancelOrder($orderId, $amount)
    {
    	ini_set('display_errors',1);
		define("ERR_CODE_OK",0x00);
		try {
	    	$paymentUrl = $this->sandbox 
	    		? 'https://sandboxsecure.mobilpay.ro'
	    		: 'https://secure.mobilpay.ro';
		   	
		   	$soap = new SoapClient($paymentUrl.'/api/payment2/?wsdl', Array('cache_wsdl' => WSDL_CACHE_NONE));
	    	
	    	$loginReq = new stdClass();
		    $loginReq->username = $this->mobilpayUsername;
		    $loginReq->password = $this->mobilpayPassword;

		    $loginResponse = $soap->logIn(Array('request' => $loginReq));
		    $sessId = $loginResponse->logInResult->id;

		    $sacId = $this->signature;

		    // Credit example
		    $req = new stdClass();
		    $req->sessionId = $sessId; // the id we previously got from the login method
		    $req->sacId = $sacId;
		    $req->orderId = $orderId;
		    $req->amount = $amount; // amount to credit

		    try {
		        $response = $soap->credit(Array('request' => $req)); //credit
				// $response = $soap->capture(Array('request' => $req)); //capture
		        if ($response->creditResult->code != ERR_CODE_OK) {
		        	return $this->response($response, $response->code);
		            // throw new Exception($response->code, $response->message);
		        }
		        return $this->response($response);
		    } catch(SoapFault $e) {
				return $this->response($e, $e->faultcode, $e->faultstring);
		        // throw new Exception($e->faultstring, $e->faultcode, $e);
		    }
		} catch(SoapFault $e) {
			return $this->response($e, $e->faultcode, $e->faultstring);
		    // throw new Exception((string)$e->faultstring, (int) $e->faultcode, $e);
		}
    }

    // TO DO: Trebuie testata
    public function cancelToken($token_id = null)
    {
    	ini_set('display_errors',1);
		define("ERR_CODE_OK",0x00);

		$paymentUrl = $this->sandbox 
    		? 'https://sandboxsecure.mobilpay.ro'
    		: 'https://secure.mobilpay.ro';
		   	
		$soap = new SoapClient($paymentUrl.'/api/payment2/?wsdl', Array('cache_wsdl' => WSDL_CACHE_NONE));

		$sacId = $this->signature;

		$req = new stdClass();

	    $account = new stdClass();
	    $account->id = $sacId;
	    $account->user_name = $this->observerUsername; // observer admin account username
	    $account->customer_ip = $_SERVER['REMOTE_ADDR'] ?? ''; // the IP address of the buyer. 
	    $account->confirm_url = $this->confirmURL;
	    $account->hash = strtoupper(sha1(strtoupper(md5($this->observerPassword))."{$account->id}"));

	    $req->account = $account;
	    $req->token = $token_id ?? $this->recurrenceTokenId;

	    try {
	        $response = $soap->cancelToken(Array('request' => $req));
	        if (isset($response->errors) && $response->errors->code != ERR_CODE_OK)
	        {
	        	return $this->response($response, $response->code);
	            // throw new Exception($response->code, $response->message);
	        }
	        return $this->response($response);
	    } catch(SoapFault $e) {
			return $this->response($e, $e->faultcode, $e->faultstring);
	        // throw new Exception((string)$e->faultstring, (int) $e->faultcode, $e);
	    }
    }

    protected function response($response, $code = 200, $msg = null)
    {
    	return (object) [
    		'code' => (int)$code,
    		'message' => $msg ?? $response->message ?? null,
    		'response' => $response,
    	];
    }
}