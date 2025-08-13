<?php 

namespace App\Invoicing\Oblio;

use App\Invoicing\InvoiceGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OblioInvoiceGateway implements InvoiceGateway
{
	private $baseURL = 'https://www.oblio.eu/api/';
	private $token = null;
	private $token_limit = null;
	private $precision = 2;
	private $currency = 'RON';
	private $language = 'RO';
	private $cif = '44117011';
	private $series_name = 'A';
	private $vat_percentage = 19;
	private $vat_included = 1; // 0 sau 1
	private $product_type = 'Serviciu'; // "Marfa", "Materii prime", "Materiale consumabile", "Semifabricate", "Produs finit", "Produs rezidual", "Produse agricole", "Animale si pasari", "Ambalaje", "Obiecte de inventar", "Serviciu"
	private $measuring_unit = 'buc';
	private $discount_type = 'valoric'; // valoric sau procentual
	private $discount_all_above = 1; // 0 sau 1

	private $client_id = null;
	private $client_secret = null;
	private $full_output = false;
	
	// private $amount = null;
	// private $type = null;
	// private $email = null;
	// private $products = [];
	// private $details = null;

	public function __construct($array)
	{
		$this->client_id 		= env('OBLIO_CLIENT_ID') ?? null;
		$this->client_secret 	= env('OBLIO_CLIENT_SECRET') ?? null;

		foreach($array as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function setAll($array)
	{
		$this->client_id 		= $array['OBLIO_CLIENT_ID'] ?? null;
		$this->client_secret 	= $array['OBLIO_CLIENT_SECRET'] ?? null;
	}

	protected function connect($array)
	{
	    $body = $array['body'] ?? null;

	    $request_id = (string) \Str::orderedUuid();

	    $headers = $array['headers'] ?? $this->getHeaders($body, $request_id);

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
	    	CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		] + ($body ? [
	    	CURLOPT_POSTFIELDS => is_array($body) ? json_encode($body) : $body,
		] : []));

	    $output = curl_exec($ch);
	    $header = curl_getinfo($ch);
	    $error = curl_error($ch);
	    $errno = curl_errno($ch);

		if($header['http_code'] != '200') {
			// dd($header,$output,$error, $errno);
		   	\Log::info('Error CURL Header');
			\Log::info($header);
		   	\Log::info('Error CURL Output');
			\Log::info($output);
			\Log::info('Error CURL Body');
			\Log::info($body);
		   	\Log::info('Error CURL Error');
			\Log::info($errno);
			\Log::info($error);
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
		return [
    		'Content-Type: application/json', 
    		'Authorization: Bearer '.$this->getToken(),
    	];
	}

	public function getToken()
	{
		if($this->token_limit < time()) {

			$requestURL = 'authorize/token';

			$data = [
				'url' => $this->baseURL.$requestURL,
				'headers' => [
    				'Content-Type: application/json', 
				],
				'body' => json_encode([
					'client_id' => $this->client_id,
					'client_secret' => $this->client_secret,
				]),
			];
			$response = json_decode($this->connect($data), true);
			$response = $this->full_output ? $response['response'] : $response;
			$this->token = isset($response['access_token']) ? $response['access_token'] : null;
			$this->token_limit = isset($response['expires_in']) ? time() + $response['expires_in'] : null;
		}

		return $this->token;
	}

	public function createInvoice($array)
	{
		$requestURL = 'docs/invoice';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'body' => [
				'cif' => $array['cif'] ?? $this->cif,
				'seriesName' => $array['series_name'] ?? $this->series_name,
				'client' => [
					'cif' => $array['client']['cif'] ?? null,
					'name' => $array['client']['name'],
					'rc' => $array['client']['rc'] ?? null,
					'email' => $array['client']['email'] ?? null,
					'phone' => $array['client']['phone'] ?? null,
					'city' => $array['client']['city'] ?? null,
                    'state' => $array['client']['state'] ?? null,
                    'country' => $array['client']['country'] ?? null,
					'address' => $array['client']['address'] ?? null,
					'contact' => $array['client']['contact'] ?? null,
				],
				'issueDate' => $array['issue_date'] ?? $array['issueDate'] ?? null,
				'dueDate' => $array['due_date'] ?? $array['dueDate'] ?? null,
				'language' => $array['language'] ?? $this->language,
				'currency' => $array['currency'] ?? $this->currency,
				'precision' => $array['precision'] ?? $this->precision,
				'products' => $array['products'] ?? $this->products,
				'collect' => isset($array['unpayed']) ? [] : ['type' => 'Card', 'documentNumber' => $array['collect']['documentNumber'] ?? $this->series_name],
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function addProduct($array)
	{
		$this->products[] = [
			'name' => $array['name'],
			'price' => $array['price'],
			'quantity' => $array['quantity'] ?? 1,
			'code' => $array['code'] ?? null,
			'description' => $array['description'] ?? null,
			'currency' => $array['currency'] ?? $this->currency,
			'measuringUnit' => $array['measuringUnit'] ?? $this->measuring_unit,
			'productType' => $array['productType'] ?? $this->product_type,
			'vatPercentage' => $array['vatPercentage'] ?? $this->vat_percentage,
			'vatIncluded' => $array['vatIncluded'] ?? $this->vat_included,
			'save' => $array['save'] ?? 0,
		];
	}

	public function addDiscount($array)
	{
		$this->products[] = [
			'name' => $array['name'],
			'discount' => $array['discount'],
			'discountType' => $array['discountType'] ?? $this->discount_type,
			'discountAllAbove' => $array['discountAllAbove'] ??  $this->discount_all_above,
		];
	}

	public function getInvoice($array)
	{
		$requestURL = 'docs/invoice';

		$query = http_build_query([
			'cif' => $array['cif'] ?? $this->cif,
			'seriesName' => $array['series_name'] ?? $this->series_name,
			'number' => $array['number']
		]);

		$data = [
			'url' => $this->baseURL.$requestURL.'?'.$query,
			'method' => 'GET',
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false;
	}

	public function getInvoicesList($array, $include_series = true)
	{
		$requestURL = 'docs/invoice/list';

		$query = http_build_query([
			'cif' => $array['cif'] ?? $this->cif,
		] + $this->addFiltersFromArray($array) + ($include_series ? [
			'seriesName' => $this->series_name,
		] : []));

		$data = [
			'url' => $this->baseURL.$requestURL.'?'.$query,
			'method' => 'GET',
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false;
	}

	protected function addFiltersFromArray(array $array)
	{
		$filters = [];
		foreach($array as $key => $value) {
			$new_key = lcfirst(\Str::studly($key));
			if(in_array($new_key, $this->allInvoiceFilters())) {
				$filters[$new_key] = $value;
			}
		}
		return $filters;
	}

	protected function allInvoiceFilters()
	{
		return [
			'cif',
			'seriesName',
			'number',
			'id',
			'draft',				// -1 - este ignorat, 0 - nu sunt draft, 1 - sunt draft
			'client',				// contine un array/map cu "cif", "email", "phone" sau "code"
			'canceled',				// -1 - este ignorat, 0 - nu sunt anulate, 1 - sunt anulate
			'issuedAfter',			// YYYY-MM-DD
			'issuedBefore',			// YYYY-MM-DD
			'withProducts',
			'withEinvoiceStatus',
			'orderBy',				// id, issueDate, number
			'orderDir',				// ASC/DESC
			'limitPerPage',			// max: 100
			'offset',
		];
	}

	public function cancelInvoice($array)
	{
		$requestURL = 'docs/invoice/cancel';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'method' => 'PUT',
			'body' => [
				'cif' => $array['cif'] ?? $this->cif,
				'seriesName' => $array['series_name'] ?? $this->series_name,
				'number' => $array['number']
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function restoreInvoice($array)
	{
		$requestURL = 'docs/invoice/restore';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'method' => 'PUT',
			'body' => [
				'cif' => $array['cif'] ?? $this->cif,
				'seriesName' => $array['series_name'] ?? $this->series_name,
				'number' => $array['number']
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function deleteInvoice($array)
	{
		$requestURL = 'docs/invoice';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'method' => 'DELETE',
			'body' => [
				'cif' => $array['cif'] ?? $this->cif,
				'seriesName' => $array['series_name'] ?? $this->series_name,
				'number' => $array['number']
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false;
	}

	public function stornInvoice($array)
	{
		$requestURL = 'docs/invoice';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'body' => [
				'cif' => $array['cif'] ?? $this->cif,
				'seriesName' => $array['series_name'] ?? $this->series_name,
				'referenceDocument' => [
			        'type' => 'Factura',
			        'seriesName' => $array['ref_series_name'] ?? $array['series_name'] ?? $this->series_name,
			        'number' => $array['number']
    			],
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false ;
	}

	public function sendInvoiceSPV($array)
	{
		$requestURL = 'docs/einvoice';

		$data = [
			'url' => $this->baseURL.$requestURL,
			'method' => 'POST',
			'body' => [
				'cif' => $array['cif'] ?? $this->cif,
				'seriesName' => $array['series_name'] ?? $this->series_name,
				'number' => $array['number']
			],
		];
		$response = json_decode($this->connect($data), true);
		return $response ? $response : false;
	}
}