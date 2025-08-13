<?php
namespace App\Imports;

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

class CargusOrdersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $unsaved = [];
        $awbs = $rows->pluck('idawb');
        $orders = Livrare::whereIn('api_shipment_id', $awbs->toArray())->pluck('api_shipment_id');
        $newOrders = $rows->whereNotIn('idawb', $orders->toArray());
        $curier = Curier::firstWhere('api_curier', 1);
        // dd($awbs, $orders, $newOrders, $rows[10]);

        if($newOrders->isNotEmpty()) {
            foreach($newOrders as $order) {
                try {

                    $user = $order['expeditorid'] == 44117011000 || $order['destinatarid'] == 44117011000
                        ? User::find(3) : User::firstWhere('email', $order['email']);
                    $ramburs = [
                        'ramburs' => $order['ramburs'] != 'Fara ramburs' ? 3 : 1,
                        'ramburs_value' => ((int)$order['rambursvaloare'] / 10000) ?: null,
                    ];
                    $options = [
                        'open_when_received' => false,
                        'retur_document' => false
                    ];
                    $swap_details = $options['retur_document'] ? [
                        'nr_parcels' => 1,
                        'total_weight' => 1,
                    ] : null;
                    $price = $curier->calculatePriceForConditions($ramburs + [
                        'options' => $options,
                        'swap_details' => $swap_details
                    ], $order['greutate'], $user);

                    $livrare = Livrare::create([
                        'user_id' => $user ? $user->id : null,
                        'email' => $user ? $user->email : $order['email'],
                        'curier' => $curier->name,
                        'api' => 2,
                        'api_shipment_id' => $order['expeditorid'],
                        'api_shipment_awb' => $order['expeditorid'],
                        'status' => $order['awbstatusexpresie'] == 'Livrat' ? 1 : 0, // status 5 nu exista in lista de status-uri DPD ca si livrat
                        'type' => $order['plic'] == 0 ? 1 : 2, 
                        'original_value' => $price,  // trebuie calculata suma din amr
                        'value' => $price,  // trebuie calculata suma din amr
                        'nr_colete' => $order['plic'] == 0 ? 0 : explode(',',(string)$order['colet'])[0],   
                        'total_weight' => $order['greutate'] < 1 ? 1 : $order['greutate'],   
                        'total_volume' => $order['greutate'] < 1 ? 1 : $order['greutate'],
                        'content' => $order['observatii'], 
                        'awb' => 1,
                        'delivered_on' => $order['awbstatusexpresie'] == 'Livrat' 
                            ? Carbon::createFromFormat('d.m.Y H:i:s', $order['data'])->format('Y-m-d H:i') 
                            : null, 
                        'pickup_day' => Carbon::createFromFormat('d.m.Y H:i:s', $order['data'])->format('Y-m-d H:i'),  
                        'start_pickup_hour' => 8,   
                        'end_pickup_hour' => 16,
                        'work_saturday' => false,
                        'open_when_received' => $options['open_when_received'],
                        'retur_document' => $options['retur_document'],
                        'swap_details' => $swap_details,
                        'ramburs' => $ramburs['ramburs'],
                        'ramburs_value' => $ramburs['ramburs_value'],
                        'assurance' => 0,
                    ]);

                    $packages = [];
                    for($i = 0 ; $i < $livrare['nr_colete'] ; $i++) {
                        $packages[] = [
                            'template_id' => 0,
                            'weight' => 10,
                            'width' => 10,
                            'length' => 10,
                            'height' => 10,
                            'volume' => round($livrare->total_weight / $livrare['nr_colete'], 2),
                        ];
                    }
                    $livrare->packages()->createMany($packages);

                    $contacts = [];
                    foreach(['exp', 'dest'] as $i => $sufix) {
                        $postcode = '-';
                        // if (preg_match('/\[(.*?)\]/', $order[$prefix.'localitate'], $match) == 1) {
                        //     $postcode = $match[1];
                        // }
                        
                        $contacts[] = [
                            'type' => $i == 0 ? 1 : 2,
                            'name' => $order['client'.$sufix],
                            'phone' => $order['telefon'.$sufix] ?? '-',
                            'email' => $order['email'.$sufix] ?? '-',
                            'country' => 'România',
                            'country_code' => strtolower($order['tara'.$sufix]),
                            'postcode' => $postcode,
                            'county' => $order['judet'.$sufix],
                            'locality' => $order['oras'.$sufix],
                            'street' => '-',
                            'street_nr' => '-',
                            'more_information' => $order['adresa'.$sufix],
                        ];
                    }
                    $livrare->contacts()->createMany($contacts);
                } catch(\Exception $e) {
                    \Log::info('The exception was created on line: ' . $e->getLine());
                    \Log::info($e->getMessage());
                    $unsaved[] = $order['expediere'];

                    if(isset($livrare)) {
                        $livrare->delete();
                    }
                }
            }
        }

        if(!empty($unsaved)) {
            session()->now('error_awbs', $unsaved);
        }
    }
}