<?php

namespace App\Traits;

use App\Invoicing\InvoiceGateway;
use App\Models\Invoice;
use App\Models\Livrare;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use ZipArchive;

trait OrderInvoiceTrait
{
    public function getInvoicePDF(Invoice $invoice)
    {   
        $data = [
            'factura' => $invoice->withMetas(),
        ];
        return PDF::loadView('invoice.invoice', $data)->setPaper('A4');
    }

    public function showInvoicePDF(Invoice $invoice, $stream = TRUE)
    {   
        if($invoice->external_link) {
            return redirect($invoice->external_link);
        }

        $pdf = $this->getInvoicePDF($invoice);

        if ($stream) {
            return $pdf->stream('Factura ' .' '. $invoice->series. $invoice->number .' '. $invoice->payed_on . '.pdf')->header('Content-Type','application/pdf');
        } else {
            return $pdf->download('Factura ' .' '. $invoice->series. $invoice->number .' '. $invoice->payed_on . '.pdf');
        }
    }

    public function downloadMultipleInvoicePDF(Request $request)
    {   
        ini_set('max_execution_time', '300');
        $path = storage_path('zip_files');
        $files = array_diff(scandir($path), array('..', '.'));

        $zip = new ZipArchive();

        # create a temp file & open it
        $tmp_file = tempnam('.','');
        $tmp_zipped_files = [];

        $zip->open($tmp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        # loop through each file
        $invoices = Invoice::whereIn('id', $request->input('invoices') ?? [])->limit(50)->get();
        foreach($invoices ?? [] as $invoice) {
            if($invoice->external_link) {
                $output = file_get_contents($invoice->external_link);
            } else {
                $pdf = $this->getInvoicePDF($invoice);
                $pdf->render();
                $output = $pdf->output();
            }

            $temp_file = tempnam($path, 'invoice_');
            $tmp_zipped_files[] = $temp_file;
            file_put_contents($temp_file, $output);
            $zip->addFile($temp_file, 'Factura '. $invoice->series. $invoice->number .' '. $invoice->payed_on . '.pdf');
            // $zip->addFromString('Factura '. $invoice->series. $invoice->number .' '. $invoice->payed_on . '.pdf', $output);
        }

        $zip->close();
        foreach ($tmp_zipped_files as $file) {
            unlink($file);
        }

        header('Content-disposition: attachment; filename=amrcolet-facturi.zip');
        header('Content-type: application/zip');
        readfile($tmp_file);
        unlink($tmp_file);

        return redirect()->back();
        
    }

    public function showPDF(Invoice $invoice, $stream = TRUE)
    {   
        if(($invoice->user_id != auth()->id()) && (auth()->user()->is_admin != '1')) {
            return abort(404);
        }

        return $this->showInvoicePDF($invoice, $stream);
    }

    public function createInvoice($total, User $user = null, $status = 0)
    {
        $invoice = Invoice::create([
            'user_id' => $user ? $user->id : null,
            'status' => $status,
            'total' => $total,
            'payed_on' => $status == 1 ? now()->format('Y-m-d') : null,
        ]);
        return $invoice;
    }

    public function addInvoiceClient(Invoice $invoice, array $client = [], $set = false)
    {
        $client = count($client) ? $client : ($invoice->user ? $invoice->user->invoiceInfo() : []);
        // Create user address
        $address = $client['address'] ?? 'Str. '.$client['street'].
        (isset($client['street_nr']) ? ' Nr. '.$client['street_nr'] : '').
        (isset($client['bl_code']) ? ', Bl. '.$client['bl_code'] : '').
        (isset($client['bl_letter']) ? ', Sc. '.$client['bl_letter'] : '').
        (isset($client['intercom']) ? ', Interfon '.$client['intercom'] : '').
        (isset($client['floor']) ? ', Etaj '.$client['floor'] : '').
        (isset($client['apartment']) ? ', Ap./Nr. '.$client['apartment'] : '');

        // Add user info to invoice
        $metas = [];
        foreach($client as $key => $value) {
            if($key == 'company_name') {
                $value != null ? ($metas['client_nume_firma'] = $value) : null;
            } else {
                $value != null ? ($metas['client_'.$key] = $value) : null;
            }
        }
        $metas = [
            'client_email' => isset($metas['email']) 
                ? $metas['email'] 
                : ($invoice->user_id != 0 || $invoice->user_id != null ? $invoice->user->email : ''),
            'client_address' => $client['address'] ?? $address,
            'client_type' => isset($client['is_company']) || isset($client['cif']) ? 2 : 1,
        ] + $metas;

        if($set) {
            $invoice->setMetas($metas);
        }
        return $metas;
    }

    public function addInvoiceProvider(Invoice $invoice, array $provider = [], $set = false)
    {
        $provider = count($provider) ? $provider : setari([
            'PROVIDER_'
        ], true, false, true);

        $metas = [
            // add provider info
            'provider_name' => $provider['PROVIDER_NAME'],
            'provider_email' => $provider['PROVIDER_EMAIL'],
            'provider_phone' => $provider['PROVIDER_PHONE'],
            'provider_address' => $provider['PROVIDER_ADDRESS'],
            'provider_nr_reg' => $provider['PROVIDER_NR_REG'],
            'provider_iban' => $provider['PROVIDER_IBAN'],
            'provider_cui' => $provider['PROVIDER_CUI'],
            'provider_cap_social' => $provider['PROVIDER_CAP_SOCIAL'],
        ];

        if($set) {
            $invoice->setMetas($metas);
        }
        return $metas;
    }

    public function addInvoiceProduct(Invoice $invoice, int $order, $product = [], $set = false)
    {
        $metas = [
            'product_name_'.$order => $product['name'],
            'product_qty_'.$order => $product['qty'] ?? $product['quantity'] ?? '1',
            'product_price_'.$order => $product['price'] ?? $product['discount'] ?? 1,
        ] + (isset($product['description']) ? [
            'product_description_'.$order => $product['description']
        ] : []) + (isset($product['nr_products']) ? [
            'product_nr_products' => $product['nr_products']
        ] : []);

        if($set) {
            $invoice->setMetas($metas);
        }
        return $metas;
    }

    public function addInvoiceVoucher(Invoice $invoice, Livrare $livrare)
    {

    }

    public function addInvoiceCreditsUsed(Invoice $invoice, Livrare $livrare)
    {

    }

    public function confirmInvoiceTemplate($invoice_id, array $livrari_id, $order_id = null, $withClientData = null) 
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $livrari = Livrare::whereIn('id', $livrari_id)->get();
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

            $metas = [];
            if($order_id != null) {
                $metas['mobilpay_order_id'] = $order_id;
            }

            // add client info if necesary
            if($withClientData) {
                if(!is_array($withClientData)) {
                    $user = $invoice->user ?? $livrare->user;
                    $client = $user->invoiceInfo();

                } else {
                    $client = $withClientData;
                }
                $metas = $this->addInvoiceClient($client + [
                    'email' => $livrare->email
                ]) + $metas;
            }

            // add provider info
            $metas = $this->addInvoiceProvider($setari) + $metas;


            $metas = [
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

    public function confirmInvoiceApi($invoice_id, $livrari_id, $order_id = null, $withClientData = null, $balanceChange = false)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $livrari = $livrari_id !== false && is_countable($livrari_id) && is_numeric($livrari_id[0] ?? null) ? Livrare::whereIn('id', $livrari_id)->get() : $livrari_id;
        if($invoice && $invoice->status != 1 && $livrari !== null) 
        {
            $api = app(InvoiceGateway::class);

            $setari = setari([
                'INVOICE_',
                'PROVIDER_'
            ], true, false, true);

            Invoice::where('id', $invoice->id)->update([
                'status' => 1,
                'series' => $setari['INVOICE_SERIES'],
                'number' => $setari['INVOICE_NR'],
                'payed_on' => now(),
            ]);

            Setting::firstWhere('name', 'INVOICE_NR')->increment('value');


            if($order_id != null) {
                $invoice->setMeta('mobilpay_order_id', $order_id);
            }

            // $client = $invoice->getMetas('client_');
            $user = $invoice->user ?? ($livrari ? $livrari[0]->user : null);
            if(!is_array($withClientData)) {
                $client = $invoice->getMetas('client_');
                $client = (empty($client) || !isset($client['email'])) && $user ? $user->invoiceInfo() : $client;

            } else {
                $client = $withClientData;
            }
            if(!isset($client['email']) && $user) {
                $client['email'] = $user->email;
            }

            if($livrari) {
                foreach ($livrari as $nr_product => $livrare) {

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

                        $product_description = $invoiceDescription;
                    } else {
                        $product_description = 'Plic';
                    }

                    $api->addProduct([
                        'name' => 'Comanda #'.$livrare->id,
                        'price' => $livrare->original_value,
                        'description' => $product_description,
                    ]);

                    // add voucher if necesary
                    if($livrare->voucher_code != null) {
                        $value = $livrare->voucher_type == '1' 
                            ? $livrare->voucher_value 
                            : round($livrare->original_value * ($livrare->voucher_value/100), 2);

                        $api->addDiscount([
                            'name' => 'Voucher "'.$livrare->voucher_code.'" '.$livrare->voucher_value.($livrare->voucher_type == '1' ? ' RON' : '%'),
                            'discount' => $value,
                        ]);
                    }

                    if($livrare->nr_credits_used > 0) {
                        $api->addDiscount([
                            'name' => __('Credite extrase din cont'),
                            'discount' => $livrare->nr_credits_used,
                        ]);

                        $invoice->total -= $livrare->nr_credits_used;
                    }
                }
            } elseif($livrari === false) {
                $api->addProduct([
                    'name' => __('Reincarcare cont cu :sum RON', ['sum' => $invoice->total]),
                    'price' => $invoice->total,
                ]);
            } else {
                return false;
            }

            if($balanceChange) {
                $user = $user ?? $invoice->user ?? ($livrari ? $livrari[0]->user : null);
                if($livrari) {
                    $user->setMeta('account_balance', '0');
                    $user->unsetMeta('expiration_date');

                    // if($user->meta('notifications_invoice_active') == 1)
                    // {
                    //     $details['action'] = 1;
                    //     $details['invoice_id'] = $invoice->id;
                    //     $details['subject'] = __('Comenzile au fost platite');
                    //     $details['title'] = __('Plata comenzilor').' '.$title.' '.('a fost reusita');
                    //     $details['body'] = __('Suma platita este de :sum RON.<br> Aveti atasata factura.<br> Veti putea descarca sau vizualiza factura in sectiunea de profil.', ['sum' => $invoice->total]);
                    //     Mail::to($user->meta('notifications_invoice_email'))->send(new \App\Mail\SendCreditPurchaseNotification($details)); 
                    // }
                } elseif($livrari === false) {
                    $account_balance = $user->meta('account_balance');
                    if($account_balance != '') {
                        $user->setMeta('account_balance', $account_balance + $invoice->total);
                    } else {
                        $user->setMeta('account_balance', $invoice->total);
                    } 
                }

            }

            try {   
                $response = $api->createInvoice([
                    'client' => [
                        'cif' => $client['cui_nif'] ?? $client['cui'] ?? $client['nif'] ?? $client['cif'] ?? null,
                        'name' => $client['nume_firma'] ?? $client['company_name'] ?? $client['last_name'].' '.$client['first_name'],
                        'rc' => $client['nr_reg_com'] ?? $client['nr_reg'] ?? $client['rc'] ?? null,
                        'email' => $client['email'],
                        'phone' => $client['phone'] ?? null,
                        'city' => $client['locality'] ?? null,
                        'state' => $client['county'] ?? null,
                        'country' => $client['country'] ?? null,
                        'address' => $client['address'] ?? null,
                        'contact' => $client['last_name'].' '.$client['first_name'] ?? null,
                    ],
                    'collect' => ['documentNumber' => $invoice->series.$invoice->number],
                    'issue_date' => date('Y-m-d')
                ]);
            } catch(\Exception $e) { \Log::info($e); }

            if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {
                $invoice->external_link = $response['data']['link'] ?? null;
                $invoice->series = $response['data']['seriesName'] ?? $invoice->series;
                $invoice->number = $response['data']['number'] ? $response['data']['number'] + 0 : $invoice->number;
            }
            $invoice->save();
            if(isset($livrare)){
                $livrare->invoice_id = $invoice->id;
                $livrare->save();
            }
            return $invoice;
        }
        return false;
    }

    public function sendInvoiceToApi($invoice_id, $payed = 1)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $api = app(InvoiceGateway::class);
        $client = $invoice->client;
        $user = $invoice->user;
        $client = (empty($client) || !isset($client['email'])) && $user ? $user->invoiceInfo() : $client;
        if(!isset($client['email']) && $user) {
            $client['email'] = $user->email;
        }
        $products = $invoice->products;
        if(empty($products)) {
            $api = $this->addProductsFromInvoice($api, $invoice, $invoice->livrari->isEmpty() ? false : $invoice->livrari);
        }

        foreach($products ?? [] as $product) {
            if($product['name'] == 'Credite extrase din cont') {
                $api->addDiscount([
                    'name' => $product['name'],
                    'discount' => $product['qty'] ?? 1,
                ]);
            } else {
                $api->addProduct([
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $product['qty'] ?? 1,
                    'description' => $product['description'] ?? null,
                ]);
            }
        }

        try {   
            $response = $api->createInvoice([
                'client' => [
                    'cif' => $client['cui_nif'] ?? $client['cui'] ?? $client['nif'] ?? $client['cif'] ?? null,
                    'name' => $client['nume_firma'] ?? $client['company_name'] ?? $client['last_name'].' '.$client['first_name'],
                    'rc' => $client['nr_reg_com'] ?? $client['nr_reg'] ?? $client['rc'] ?? null,
                    'email' => $client['email'],
                    'phone' => $client['phone'] ?? null,
                    'city' => $client['locality'] ?? null,
                    'state' => $client['county'] ?? null,
                    'country' => $client['country'] ?? null,
                    'address' => $client['address'] ?? null,
                    'contact' => $client['last_name'].' '.$client['first_name'] ?? null,
                ],
                'issue_date' => date('Y-m-d'),
            ] + ($payed === 1 ? [
                'collect' => ['documentNumber' => $invoice->series.$invoice->number],
            ] : [
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'unpayed' => 1
            ]));
        } catch(\Exception $e) { 
            \Log::info($e);
            return back()->withErrors(['error' => __('Eroare la trimiterea in platforma de facturare. Contactati un developer.')]);
        }

        if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {
            $invoice->external_link = $response['data']['link'] ?? null;
            $invoice->series = $response['data']['seriesName'] ?? $invoice->series;
            $invoice->number = $response['data']['number'] ? $response['data']['number'] + 0 : $invoice->number;
        } else {
            \Log::info($response);
            return back()->withErrors(['error' => __('Eroare la preluarea informatiilor din platforma de facturare. Contactati un developer.')]);
        }
        $invoice->save();

        return back()->with(['status' => __('Factura a fost trimisa in platforma de facturare.')]);
    }

    public function updateInvoiceFromApi($invoice_id)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $api = app(InvoiceGateway::class);

        try {   
            $response = $api->getInvoicesList(['number' => $invoice->number, 'withProducts' => 1, 'withEinvoiceStatus' => 1]);
        } catch(\Exception $e) { 
            \Log::info($e);
            return back()->withErrors(['error' => __('Eroare la trimiterea in platforma de facturare. Contactati un developer.')]);
        }

        if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {
            $info = $response['data'][0];
            $api_client = $info['client'];
            $api_products = $info['products'];
            $client = [
                'cui_nif' => $api_client['cif'] ?? null,
                'name' => $api_client['name'],
                'nr_reg_com' => $api_client['rc'] ?? null,
                'address' => $api_client['address'],
                'county' => $api_client['state'],
                'locality' => $api_client['city'],
                'country' => $api_client['country'],
                'contact' => $api_client['contact'],
                'phone' => $api_client['phone'],
                'email' => $api_client['email'],
            ];
            $metas = $this->addInvoiceClient($invoice, $client);

            $invoice->unsetMetas('product_');
            foreach ($api_products as $order => $product) {
                $metas += $this->addInvoiceProduct($invoice, $order, $product + ['nr_products' => count($product)]);
            }

            $invoice->status = $info['canceled'] == 1 ? 2 : 1;
            $invoice->total = $info['total'] ?? null;
            $invoice->payed_on = $info['issueDate'] ?? null;
            $invoice->spv = isset($info['einvoiceStatus']['code']) && $info['einvoiceStatus']['code'] == 0 ? 1 : null;
            $invoice->save();

            $invoice->setMetas($metas);
        } else {
            \Log::info($response);
            return back()->withErrors(['error' => __('Eroare la preluarea informatiilor din platforma de facturare. Contactati un developer.')]);
        }

        return back()->with(['status' => __('Factura a fost actualizata cu informatiile din platforma de facturare.')]);
    }

    public function addProductsFromInvoice($api, $invoice, $livrari_id = false)
    {
        $livrari = $livrari_id !== false && is_countable($livrari_id) && is_numeric($livrari_id[0] ?? null) 
            ? Livrare::whereIn('id', $livrari_id)->get() : $livrari_id;
        if($livrari) {
            foreach ($livrari as $nr_product => $livrare) {

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

                    $product_description = $invoiceDescription;
                } else {
                    $product_description = 'Plic';
                }

                $api->addProduct([
                    'name' => 'Comanda #'.$livrare->id,
                    'price' => $livrare->original_value,
                    'description' => $product_description,
                ]);

                // add voucher if necesary
                if($livrare->voucher_code != null) {
                    $value = $livrare->voucher_type == '1' 
                        ? $livrare->voucher_value 
                        : round($livrare->original_value * ($livrare->voucher_value/100), 2);

                    $api->addDiscount([
                        'name' => 'Voucher "'.$livrare->voucher_code.'" '.$livrare->voucher_value.($livrare->voucher_type == '1' ? ' RON' : '%'),
                        'discount' => $value,
                    ]);
                }

                if($livrare->nr_credits_used > 0) {
                    $api->addDiscount([
                        'name' => __('Credite extrase din cont'),
                        'discount' => $livrare->nr_credits_used,
                    ]);
                }
            }
            Setting::firstWhere('name', 'INVOICE_NR')->increment('value');
        } elseif($livrari === false) {
            $api->addProduct([
                'name' => __('Reincarcare cont cu :sum RON', ['sum' => $invoice->total]),
                'price' => $invoice->total,
            ]);
            Setting::firstWhere('name', 'INVOICE_NR')->increment('value');
        }
        return $api;
    }

    public function cancelInvoiceApi($invoice_id)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $api = app(InvoiceGateway::class);

        try {   
            $response = $api->cancelInvoice(['number' => $invoice->number]);
        } catch(\Exception $e) { 
            \Log::info($e);
            return back()->withErrors(['error' => __('Eroare la trimiterea in platforma de facturare. Contactati un developer.')]);
        }

        if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {

            $invoice->status = 2;
            $invoice->save();
        } else {
            \Log::info($response);
            return back()->withErrors(['error' => __('Eroare la preluarea informatiilor din platforma de facturare. Contactati un developer.')]);
        }

        return back()->with(['status' => __('Factura a fost anulata prin platforma de facturare.')]);
    }

    public function restoreInvoiceApi($invoice_id)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $api = app(InvoiceGateway::class);

        try {   
            $response = $api->restoreInvoice(['number' => $invoice->number]);
        } catch(\Exception $e) { 
            \Log::info($e);
            return back()->withErrors(['error' => __('Eroare la trimiterea in platforma de facturare. Contactati un developer.')]);
        }

        if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {

            $invoice->status = $invoice->credited_by ? 3 : 1;
            $invoice->save();
        } else {
            \Log::info($response);
            return back()->withErrors(['error' => __('Eroare la preluarea informatiilor din platforma de facturare. Contactati un developer.')]);
        }

        return back()->with(['status' => __('Factura a fost restaurata prin platforma de facturare.')]);
    }

    public function sendInvoiceSPVApi($invoice_id)
    {
        $invoice = $invoice_id instanceof \App\Models\Invoice ? $invoice_id : Invoice::firstWhere('id', $invoice_id);
        $api = app(InvoiceGateway::class);

        try {   
            $response = $api->sendInvoiceSPV(['number' => $invoice->number]);
        } catch(\Exception $e) { 
            \Log::info($e);
            return back()->withErrors(['error' => __('Eroare la trimiterea in platforma de facturare. Contactati un developer.')]);
        }

        if(isset($response) && isset($response['status']) && isset($response['data']) && $response['status'] === 200) {

            $invoice->spv = 1;
            $invoice->save();
        } else {
            \Log::info($response);
            return back()->withErrors(['error' => __('Eroare la preluarea informatiilor din platforma de facturare. Contactati un developer.')]);
        }

        return back()->with(['status' => __('Factura a fost restaurata prin platforma de facturare.')]);
    }
}
