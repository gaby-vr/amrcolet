<?php 

namespace App\Billing\LibraBank;

use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use phpseclib3\File\X509;
use DateTime;
use stdClass;
use SoapClient;
use SoapFault;
use Exception;

class LibraBankGateway implements PaymentGateway
{
	private $baseURL = 'https://api.librabank.ro:8243/';
	private $testURL = 'https://api-test.librabank.ro:8243/';
	private $token = null;
	private $token_limit = null;
	private $currency = 'RON';
	private $amount = null;
	private $type = null;
	private $email = null;
	private $sandbox = false;
	private $full_output = true;
	private $signature = null;
	private $params = null;
	private $details = null;
	private $username = null;
	private $password = null;
	private $customer_key = null;
	private $customer_secret = null;
	private $debtor_iban = null;
	private $ssl_public_certificate = null;
	private $cert_subdomain_prefixes = ['', 'www_', 'cpanel_', 'webdisk_'];

	public function __construct($array)
	{

		$this->sandbox 			= env('LIBRA_SANDBOX') ?? true;
		$this->username 		= env('LIBRA_USERNAME') ?? null;
		$this->password 		= env('LIBRA_PASSWORD') ?? null;
		$this->customer_key 	= env('LIBRA_CUSTOMER_KEY') ?? null;
		$this->customer_secret 	= env('LIBRA_CUSTOMER_SECRET') ?? null;
		$this->debtor_iban 		= env('LIBRA_DEBTOR_IBAN') ?? null;

		if($this->sandbox) {
			$this->baseURL 			= $this->testURL;
			$this->username 		= env('LIBRA_USERNAME_SANDBOX') ?? null;
			$this->password 		= env('LIBRA_PASSWORD_SANDBOX') ?? null;
			$this->customer_key 	= env('LIBRA_CUSTOMER_KEY_SANDBOX') ?? null;
			$this->customer_secret 	= env('LIBRA_CUSTOMER_SECRET_SANDBOX') ?? null;
		}

		foreach($array as $key => $value) {
			$this->{$key} = $value;
		}

		foreach ($this->cert_subdomain_prefixes as $prefix) {
			$ssl_public_certificate = $this->getSSLCertificate($prefix);
			if(
				isset($ssl_public_certificate['data']['validTo_time_t'])
				&& $ssl_public_certificate['data']['validTo_time_t'] > time()
				&& date('Y-m-d', $ssl_public_certificate['data']['validTo_time_t'] ?? null) > date('Y-m-d')
			) {
				$this->ssl_public_certificate = $ssl_public_certificate;
				break;
			}
		}
		if($this->ssl_public_certificate === null) {
			$this->ssl_public_certificate = $this->getSSLCertificate($prefix);
		}
	}

	public function setAll($array)
	{
		$this->username 		= $array['LIBRA_USERNAME'] ?? null;
		$this->password 		= $array['LIBRA_PASSWORD'] ?? null;
		$this->customer_key 	= $array['LIBRA_CUSTOMER_KEY'] ?? null;
		$this->customer_secret 	= $array['LIBRA_CUSTOMER_SECRET'] ?? null;
	}

	public function getSSLCertificateData()
	{
		return $this->ssl_public_certificate;
	}

	public function getSSLCertificateValidToTime()
	{
		return $this->ssl_public_certificate ? $this->ssl_public_certificate['data']['validTo_time_t'] : null;
	}

	public function getSSLCertificateDataThumbprint()
	{
		return openssl_x509_fingerprint($this->ssl_public_certificate['raw']);
	}

	protected function connect($array)
	{
		if(!$this->ssl_public_certificate) {
			return false;
		}


	    $body = $array['body'] ?? null;

	    $request_id = (string) \Str::orderedUuid();


	    $headers = $array['headers'] ?? $this->getHeaders($body, $request_id);

    	// !isset($array['headers']) ? dd($headers, $body, $array['url']) : null;
	    $ch = curl_init();

	    curl_setopt_array($ch, [
	    	CURLOPT_URL => $array['url'],
	    	CURLOPT_CUSTOMREQUEST => $array['method'] ?? "POST",
	    	CURLOPT_HTTPHEADER => $headers,
	    	CURLINFO_HEADER_OUT => true,
	    	CURLOPT_HEADER => 0,
	    	CURLOPT_RETURNTRANSFER => true,
	    	// Tries before timeout
	    	CURLOPT_CONNECTTIMEOUT => 2,
	    	// Timeout in seconds
	    	CURLOPT_TIMEOUT => 30
		] + ($body ? [
	    	CURLOPT_POSTFIELDS => mb_convert_encoding(json_encode($body), 'UTF-8'),
		] : []));

	    $output = curl_exec($ch);
	    $header = curl_getinfo($ch);
	    $error = curl_error($ch);
	    $errno = curl_errno($ch);

		if($header['http_code'] != '200') {
			// dd($header,$output,$error, $errno);
		   	// \Log::info('Error CURL Header');
			// \Log::info($header);
		   	// \Log::info('Error CURL Output');
			// \Log::info($output);
		   	// \Log::info('Error CURL Error');
			// \Log::info($errno);
			// \Log::info($error);
		}

	    curl_close($ch);

	    return $this->full_output ? json_encode([
	    	'guid' => $request_id,
	    	'status' => $header['http_code'],
	    	'header' => $headers,
	    	'body' => $body,
	    	'response' => json_decode($output, true) !== null ? json_decode($output, true) : $output,
	    	'response_header' => $header,
	    	'error' => $error,
	    	'error_code' => $errno,
	    ]) : $output;
	}

	public function getHeaders($body, $request_id)
	{
		$digest = base64_encode(hash('sha256', $body ? mb_convert_encoding(json_encode($body), 'UTF-8') : null, true));
		$signing_string = implode("\n", [
    		'digest: SHA-256='.$digest,
    		'x-request-id: '.$request_id, 
	    ]);
	    // $data = $signing_string;

		// $hashed_string = hex2bin("3031300d060960864801650304020105000420") . hash('sha256', $signing_string, true);
		// openssl_private_encrypt($hashed_string, $signature, $this->ssl_public_certificate['private_key'], OPENSSL_PKCS1_PADDING);
		// $public = PublicKeyLoader::load($this->ssl_public_certificate['raw']);
		// $private = RSA::loadFormat('PKCS1', $this->ssl_public_certificate['private_key'])->withPadding(RSA::SIGNATURE_PKCS1)->withHash("sha256");

		// dd(openssl_x509_read($this->ssl_public_certificate['raw']));

		// $x509 = new X509();
		// $x509->setPrivateKey($private);
		// $cert = $x509->loadX509($this->ssl_public_certificate['raw']);
		// $public = $x509->getPublicKey();
		// $private = $public->asPrivateKey();
		// dd($x509, $x509->validateSignature(false), $public, $x509->saveX509($cert),$cert );
		// dd(openssl_pkey_get_private(openssl_x509_read($this->ssl_public_certificate['raw'])));

		// dd($rsa);
		// $private = RSA::createKey()->withPadding(RSA::SIGNATURE_PKCS1)->withHash("sha256");
		// $public = $private->getPublicKey();

		//Set PKCS1 mode
		// $rsa = $rsa->withPadding(RSA::SIGNATURE_PKCS1)->withHash("sha256");
		// $signature = $private->sign($signing_string);
		// dd($signature);
		// dd($public->toString('PKCS1'));
		// dd($public->verify($signing_string, $signature));

		// dd($signature);
	    openssl_sign($signing_string, $signature, $this->ssl_public_certificate['private_key'], OPENSSL_ALGO_SHA256);
	    // dd(openssl_verify($signing_string, $signature, $this->ssl_public_certificate['raw'], OPENSSL_ALGO_SHA256));

	    // $certificate = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----'], '', $this->ssl_public_certificate['content']);
	    $certificate = $this->ssl_public_certificate['content'];

	    // dd(hash('sha256', base64_decode($certificate), false));

	    // corect thumbprint 
	    // dd(openssl_x509_fingerprint($this->ssl_public_certificate['raw']));

	    // $certificate = str_replace(["\n","\r"], '', $x509->saveX509($cert));
	    // $x5092 = new X509();
		// $cert2 = $x5092->loadX509($x509->saveX509($cert));
	    // dd($x5092);

		return [
    		'Content-Type: application/json', 
    		'Digest: SHA-256='.$digest,
    		'X-Request-ID: '.$request_id, 
    		'TPP-Signature-Certificate: '.$certificate,
    		'Signature: keyId="SN='.$this->checkNegativeSerialNumber($this->ssl_public_certificate['data']['serialNumberHex']).'",algorithm="'.strtolower($this->ssl_public_certificate['data']['signatureTypeSN']).'",headers="digest x-request-id",signature="'.base64_encode($signature).'"',
    		'Authorization: Bearer '.$this->getToken(),
    	];
	}

	public function getToken()
	{
		if($this->token_limit < time()) {

			$requestURL = 'token';

			$data = [
				'url' => $this->baseURL.$requestURL,
				'headers' => [ 
					'Authorization: Basic '.base64_encode($this->customer_key.':'.$this->customer_secret),
    				'Content-Type: application/json', 
				],
				'body' => [
					'grant_type' => 'client_credentials',
					'scope' => 'default',
				],
			];
			$response = json_decode($this->connect($data), true);
			$response = $this->full_output ? $response['response'] : $response;
			$this->token = isset($response['access_token']) ? $response['access_token'] : null;
			$this->token_limit = isset($response['expires_in']) ? time() + $response['expires_in'] : null;
		}

		return $this->token;
	}

	/* 
	 	"The serial number MUST be a positive integer"
       	"Conforming CAs MUST NOT use serialNumber values longer than 20 octets."
        -- https://tools.ietf.org/html/rfc5280#section-4.1.2.2

       	for the integer to be positive the leading bit needs to be 0 hence the
       	application of a bitmap
       	HEX <-> BINARY <-> SIGN
		0-7 <-> 0000-0111 <-> pos
		8-F <-> 1000-1111 <-> neg
     */
	protected function checkNegativeSerialNumber($serial_number)
	{
		if(in_array($serial_number[0], ['8','9','A','B','C','D','E','F'])) {
			return '00'.$serial_number;
		}
        return $serial_number;
	}

	// BC Math Library required
	protected function bchexdec($hex)
	{
	    $dec = 0;
	    $len = strlen($hex);
	    for ($i = 1; $i <= $len; $i++) {
	        $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
	    }
	    return $dec;
	}

	// BC Math Library required
	protected function bcdechex($dec)
	{
	    $hex = '';
	    do {
	        $last = bcmod($dec, 16);
	        $hex = dechex($last) . $hex;
	        $dec = bcdiv(bcsub($dec, $last), 16);
	    } while ($dec > 0);
	    return $hex;
	}

	protected function canUseInstantPayment($value)
	{
		$bank_codes = ['BTRL','CECE','RNCB','EGNA','CARP','RZBR','BRDE','WBAN','INGB'];
		if (preg_match('/RO[0-9]{2}('.implode('|', $bank_codes).')[A-Z0-9]{16}/', $value)) {
            return true;
        } else {
        	return false;
        }
	}

	public function setPaymentIntent($array)
	{
		$requestURL = $this->canUseInstantPayment($array['creditor_iban']) 
			? 'INSTANT_PAYMENTS_LE_API/v1/INSTANT-Transfer/whitelist'
			: 'PAYMENTS_LE_API/v1/RON-Transfer/whitelist';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'body' => [
				'instructedAmount' => [
					'currency' => $this->currency,
					'amount' => $array['amount'],
				],
				'debtorAccount' => [
					'iban' => $this->debtor_iban
				],
				'creditorName' => $array['creditor_name'],
				'creditorAccount' => [
					'iban' => $array['creditor_iban']
				],
				'remittanceInformationUnstructured' => $array['description'] ?? '',
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function getPaymentStatus($array)
	{
		$requestURL = $this->canUseInstantPayment($array['iban']) 
			? __('INSTANT_PAYMENTS_LE_API/v1/:payment_id/status', ['payment_id' => $array['payment_id']])
			: __('PAYMENTS_LE_API/v1/:payment_id/status', ['payment_id' => $array['payment_id']]);

		$data = [
			'url' => $this->baseURL.$requestURL,
			'method' => 'GET',
			'body' => null,
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function getSSLCertificate($add_prefix = '')
	{
		$cert_file = $this->getSSLCertificateFileName(true, false, $add_prefix);
		$key_file = $this->getSSLPrivateKey(true, $add_prefix);
		$content = file_exists($cert_file) ? file_get_contents($cert_file) : null;
		$private_key = file_exists($key_file) ? file_get_contents($key_file) : null;
		return $content ? [
			'raw' => $content,
			'content' => str_replace(["\n","\r"], '', $content),
			'data' => openssl_x509_parse($content, true),
			'private_key' => $private_key,
		] : null;
	}

	public function getSSLCertificateFileName($full_path = true, $secret_key_code = false, $add_prefix = '')
	{
		$path = '/home/amrcolet/ssl/certs';
		foreach (array_diff(scandir($path), ['.','..']) as $filename) {
			if(preg_match('/^'.$add_prefix.'amrcolet_ro_(.*)\.crt$/', $filename, $matches)) { 
				return $secret_key_code ? $matches[1] : ($full_path ? $path.'/' : '').$filename; 
			}
		}
		return null;
	}

	public function getSSLPrivateKey($full_path = true, $add_prefix = '')
	{
		$path = '/home/amrcolet/ssl/keys';
		$secret_key_file = substr($this->getSSLCertificateFileName(false, true, $add_prefix), 0, 11);

		foreach (array_diff(scandir($path), ['.','..']) as $filename) {
			if(preg_match('/^('.$secret_key_file.'.*)\.key$/', $filename, $matches)) { 
				return ($full_path ? $path.'/' : '').$matches[0]; 
			}
		}
		return null;
	}
}