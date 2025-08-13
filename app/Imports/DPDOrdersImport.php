<?php
namespace App\Imports;

use App\Models\Borderou;
use App\Models\Livrare;
use App\Models\CodPostal;
use App\Models\Contact;
use App\Models\Curier;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DPDOrdersImport implements ToCollection, WithHeadingRow
{
    protected $exclude_borderou = null;
    protected $borderouri = [];

    public function __construct($exclude = null)
    {
        $this->exclude_borderou = $exclude ? true : null;
    }

    public function collection(Collection $rows)
    {
        $unsaved = [];
        $awbs = $rows->where('tip', 'Standard')->pluck('expediere');
        // $orders = Livrare::whereIn('api_shipment_awb', $awbs->toArray())->get();
        // // $a = $orders->delete();
        // foreach($orders as $order) {
        //     $order->delete();
        // }
        // dd($orders);
        $orders = Livrare::whereIn('api_shipment_awb', $awbs->toArray())->pluck('api_shipment_awb');
        $newOrders = $rows->where('tip', 'Standard')->whereNotIn('expediere', $orders->toArray());
        $curier = Curier::firstWhere('api_curier', 2);
        // dd($awbs, $orders, $newOrders, $rows->where('tip', 'Standard'), $this->exclude_borderou);

        if($newOrders->isNotEmpty()) {
            foreach($newOrders as $order) {
                // dd($order['preluat'], Carbon::createFromFormat('d.m.Y H:i', $order['preluat'])->format('Y-m-d H:i'));
                try {
                    $user = $order['expeditorid'] == 44117011000 || $order['destinatarid'] == 44117011000
                        ? User::find(3) : User::firstWhere('email', $order['expeditoremail']);

                    $import_prices = [
                        'min_kg' => $order['min_kg'] ?? 3,
                        'min_kg_price' => $order['min_kg_price'] ?? 7.24,
                        'kg_aditional' => $order['kg_aditional'] ?? 1.14,
                    ];

                    if($user && (isset($order['min_kg_price']) && $order['min_kg_price'] !== null || $this->exclude_borderou)) {
                        $user->import_volum_price = $import_prices['kg_aditional'];
                        $user->import_prices = [$import_prices['min_kg'] => $import_prices['min_kg_price']];
                        $new_price = 1;
                    }

                    $repayment = $user ? $user->getMetas('repayment_') : [];

                    $ramburs = [
                        'ramburs' => $order['plata_la_livrare'] ? 3 : 1,
                        'ramburs_value' => $order['plata_la_livrare'] ?: null,
                    ];
                    $options = [
                        'open_when_received' => $order['deschidere_la_livrare'] && $this->exclude_borderou == null ? true : false,
                        'retur_document' => $order['retur_documente'] || $order['servicii_retur'] ? true : false
                    ];
                    $swap_details = $options['retur_document'] ? [
                        'nr_parcels' => 1,
                        'total_weight' => 1,
                    ] : null;
                    $price = $curier->calculatePriceForConditions($ramburs + [
                        'options' => $options,
                        'swap_details' => $swap_details
                    ], $order['greutate'], $user, null, $new_price ?? null);
                    // dd($price, $user, $order['expediere'], $order['greutate']);

                	$livrare = Livrare::create([
                        'user_id' => $user ? $user->id : null,
                        'email' => $user ? $user->email : $order['expeditoremail'],
                        'curier' => $curier->name,
                        'api' => 2,
                        'api_shipment_id' => $order['expediere'],
                        'api_shipment_awb' => $order['expediere'],
                        'status' => $order['status'] == 5 ? 1 : 0, // status 5 nu exista in lista de status-uri DPD ca si livrat
                        'type' => $order['ambalaj'] == 'PLIC' ? 2 : 1, 
                        'original_value' => $price,  // trebuie calculata suma din amr
                        'value' => $price,  // trebuie calculata suma din amr
                        'nr_colete' => $order['numar_colete'],   
                        'total_weight' => $order['greutate'] < 1 ? 1 : $order['greutate'],   
                        'total_volume' => $order['greutate'] < 1 ? 1 : $order['greutate'],
                        'content' => $order['continut'], 
                        'awb' => 1,
                        'delivered_on' => $order['status'] == 5 
                            ? Carbon::createFromFormat('d.m.Y H:i', $order['livraretimp'])->format('Y-m-d H:i') 
                            : null, 
                        'pickup_day' => Carbon::createFromFormat('d.m.Y H:i', $order['preluat'])->format('Y-m-d H:i'),  
                        'start_pickup_hour' => 8,   
                        'end_pickup_hour' => 16,
                        'work_saturday' => false,
                        'open_when_received' => $options['open_when_received'],
                        'retur_document' => $options['retur_document'],
                        'swap_details' => $swap_details,
                        'ramburs' => $ramburs['ramburs'],
                        'ramburs_value' => $ramburs['ramburs_value'],
                        'iban' => $ramburs['ramburs_value'] ? ($repayment['iban'] ?? null) : null,
                        'titular_cont' => $ramburs['ramburs_value'] ? ($repayment['card_owner_name'] ?? null) : null,
                        'assurance' => 0,
                        'created_at' => Carbon::createFromFormat('d.m.Y H:i', $order['preluat'])->format('Y-m-d H:i'),
                    ]);

                    $packages = [];
                    for($i = 0 ; $i < $order['numar_colete'] ; $i++) {
                        $packages[] = [
                            'template_id' => 0,
                            'weight' => 1,
                            'width' => 10,
                            'length' => 10,
                            'height' => 10,
                            'volume' => round($livrare->total_weight / $order['numar_colete'], 2),
                        ];
                    }
                    $livrare->packages()->createMany($packages);

                    $contacts = [];
                    foreach(['expeditor', 'destinatar'] as $i => $prefix) {
                        $postcode = '-';
                        if (preg_match('/\[(.*?)\]/', $order[$prefix.'localitate'], $match) == 1) {
                            $postcode = $match[1];
                        }

                        if($postcode !== '-') {
                            $db_postcode = CodPostal::firstWhere('cod_postal', $postcode);
                            $county = $db_postcode ? $db_postcode->judet : '-';
                        }
                        
                        $contacts[] = [
                            'type' => $i == 0 ? 1 : 2,
                            'name' => $order[$prefix.'sediu'] ?? $order[$prefix.'nume'],
                            'phone' => $order[$prefix.'telefon'],
                            'email' => $order[$prefix.'email'],
                            'country' => 'România',
                            'country_code' => strtolower($order[$prefix.'cod_tara']),
                            'postcode' => $postcode,
                            'county' => $county,
                            'locality' => $order[$prefix.'localitate'],
                            'street' => '-',
                            'street_nr' => '-',
                            'more_information' => $order[$prefix.($i == 0 ? 'address' : 'adresa')].($order[$prefix.'adresa_nota'] ? ' / '.$order[$prefix.'adresa_nota'] : ''),
                        ];
                    }
                    $livrare->contacts()->createMany($contacts);
                    if($this->exclude_borderou && $user && $ramburs['ramburs_value']) {
                        $this->borderouri[$user->id][] = [
                            'awb' => $livrare->api_shipment_id,
                            'sender_name' => $order['expeditorsediu'] ?? $order['expeditornume'],
                            'receiver_name' => $order['destinatarsediu'] ?? $order['destinatarnume'],
                            'order_created_at' => $livrare->created_at,
                            'payment' => $ramburs['ramburs_value'],
                            'iban' => $livrare->iban,
                            'account_owner' => $livrare->titular_cont,
                        ];
                    }
                } catch(\Exception $e) {
                    \Log::info('The exception was created on line: ' . $e->getLine());
                    \Log::info($e->getMessage());
                    $unsaved[] = $order['expediere'];

                    if(isset($livrare)) {
                        $livrare->delete();
                    }
                }
            }

            if($this->exclude_borderou && !empty($this->borderouri)) {
                foreach($this->borderouri as $user_id => $borderouri_awbs) {
                    $collection = collect($borderouri_awbs ?? []);
                    $borderou = Borderou::create([
                        'user_id' => $user_id, 
                        'total' => $collection->sum('payment') ?? 0,
                        'start_date' => $collection->min('order_created_at') ?? now()->format('Y-m-d H:i'),
                        'end_date' => $collection->max('order_created_at') ?? now()->format('Y-m-d H:i'),
                        'payed_at' => now()->format('Y-m-d H:i'),
                        'exclude' => 1,
                    ]);

                    $borderou->borderouAwbs()->createMany($borderouri_awbs);
                }
            }
        }

        if(!empty($unsaved)) {
            session()->now('error_awbs', $unsaved);
        }
    }
}