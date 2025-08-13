<?php

namespace App\Traits;

use App\Models\Repayment;
use App\Models\Livrare;
use App\Models\LivrareStatus;
use App\Models\LivrareCancelRequest;
use App\Courier\CourierGateway;
use Log;
use Mail;
use DateTime;

trait OrderStatusCheckTrait
{
    public function checkUrgentCargusOrder(Livrare $livrare)
    {
        $api = app(CourierGateway::class, ['type' => 1]);

        $awb = $api->trackParcels([
            'barCode' => $livrare->api_shipment_awb, 
            'TotalWeight' => $livrare->total_weight,
            'createdAt' => $livrare->created_at
        ]);
        if($awb && isset($awb[0]['Event']) && count($awb[0]['Event']) > 0) {
            $last = count($awb[0]['Event']) - 1;
            // $livrareStatus = LivrareStatus::updateOrCreate([
            //     'api' => '1',
            //     'api_shipment_id' => $livrare->api_shipment_id,
            //     'api_parcel_id' => $livrare->api_shipment_awb,
            //     'api_status_code' => $awb[0]['Event'][$last]['EventId'],
            //     'description' => $awb[0]['Event'][$last]['Description'],
            // ]);
            $this->updateCurierStatusList([[
                'api' => '1',
                'api_shipment_id' => $livrare->api_shipment_id,
                'api_parcel_id' => $livrare->api_shipment_awb,
                'api_status_code' => $awb[0]['Event'][$last]['EventId'],
                'description' => $awb[0]['Event'][$last]['Description'],
            ]]);

            $this->getCurierStatus($livrare, $awb[0]['Event'][$last]['EventId']);
        }
    }

    public function checkDPDOrder(Livrare $livrare)
    {
        $api = app(CourierGateway::class, ['type' => 2]);

        $array['parcels'] = $api->getOrderParcels(['shipmentId' => $livrare->api_shipment_id]);
        $parcelsStatus = $api->trackParcels($array);
        if($parcelsStatus) {
            $max = -999;
            $logged = 0;
            $livrareStatusList = [];
            foreach($parcelsStatus as $parcelStatus) {
                if(isset($parcelStatus['operations'][0]['operationCode'])) {
                    // $livrareStatus = LivrareStatus::updateOrCreate([
                    //     'api' => '2',
                    //     'api_shipment_id' => $livrare->api_shipment_id,
                    //     'api_parcel_id' => $parcelStatus['parcelId'],
                    //     'api_status_code' => $parcelStatus['operations'][0]['operationCode'],
                    //     'description' => $parcelStatus['operations'][0]['description'],
                    // ]);
                    $livrareStatusList[] = [
                        'api' => '2',
                        'api_shipment_id' => $livrare->api_shipment_id,
                        'api_parcel_id' => $parcelStatus['parcelId'],
                        'api_status_code' => $parcelStatus['operations'][0]['operationCode'],
                        'description' => $parcelStatus['operations'][0]['description'],
                    ];
                    $max = $max > $parcelStatus['operations'][0]['operationCode'] ? $max : $parcelStatus['operations'][0]['operationCode'];
                } elseif($logged === 0 && isset($parcelStatus['operations'][0])) {
                    try {
                        \Log::info('Status neinregistrat:');
                        \Log::info($parcelStatus);
                        \Log::info($parcelStatus['operations']);
                        $logged = 1;
                    } catch (Exception $e) {}
                }
            }

            $this->updateCurierStatusList($livrareStatusList ?? null);

            $this->getCurierStatus($livrare, $max);
        }
    }

    public function checkGLSOrder(Livrare $livrare)
    {
        $api = app(CourierGateway::class, ['type' => 3]);

        if($livrare->awbLabels) {
            $parcelList = $livrare->awbLabels->parcel_awb_list ?? [];
            $parcelsStatus = [];
            foreach($parcelList as $index => $parcel) {
                $parcelsStatus[$index] = $api->trackParcels(['ParcelNumber' => $parcel]);
                $parcelsStatus[$index] = is_array($parcelsStatus[$index]) 
                    ? $parcelsStatus[$index] + ['ParcelNumber' => $parcel] : false;
            }
            if($parcelsStatus) {
                $max = -999;
                $logged = 0;
                $livrareStatusList = [];
                foreach($parcelsStatus as $parcelStatus) {
                    if(isset($parcelStatus[0]['StatusCode'])) {
                        $lastParcelStatus = $parcelStatus[0];

                        // $livrareStatus = LivrareStatus::updateOrCreate([
                        //     'api' => '3',
                        //     'api_shipment_id' => $livrare->api_shipment_id,
                        //     'api_parcel_id' => $parcelStatus['ParcelNumber'],
                        //     'api_status_code' => $lastParcelStatus['StatusCode'],
                        //     'description' => $lastParcelStatus['StatusDescription'],
                        // ]);
                        $livrareStatusList[] = [
                            'api' => '3',
                            'api_shipment_id' => $livrare->api_shipment_id,
                            'api_parcel_id' => $parcelStatus['ParcelNumber'],
                            'api_status_code' => $lastParcelStatus['StatusCode'],
                            'description' => $lastParcelStatus['StatusDescription'],
                        ];
                        $max = $max > $lastParcelStatus['StatusCode'] ? $max : $lastParcelStatus['StatusCode'];
                    } elseif($logged === 0 && isset($lastParcelStatus['StatusCode'])) {
                        try {
                            \Log::info('Status neinregistrat:');
                            \Log::info($parcelStatus);
                            $logged = 1;
                        } catch (Exception $e) {}
                    }
                }

                $this->updateCurierStatusList($livrareStatusList ?? null);

                $this->getCurierStatus($livrare, $max);
            }
        }
    }

    protected function updateCurierStatusList($livrareStatusList)
    {
        try {
            if(is_array($livrareStatusList) && $livrareStatusList !== []) {
                LivrareStatus::upsert($livrareStatusList, ['api','api_shipment_id','api_parcel_id','api_status_code','description']);
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
    }

    public function getCurierStatus(Livrare $livrare, $api_status)
    {
        // $status = $this->getOrderStatusPerCurierStatus($api_status, $livrare->api);
        $status = $this->getSpecialStatusForCurier($livrare, $api_status);
        if($status !== null && $livrare->status != $status) {
            $livrare->status = $status;
            if($livrare->status === 1) {
                $livrare->status = 1;
                $livrare->delivered_on = date('Y-m-d');
                if($livrare->ramburs > 1 && Repayment::where('awb', $livrare->api_shipment_awb)->count() > 0) {
                    Repayment::firstWhere('awb', $livrare->api_shipment_awb)->update([
                        'date_delivered' => date('Y-m-d'),
                        'payed_on' => date('Y-m-d'),
                        'status' => 1,
                    ]);
                }
            }
            if($livrare->status != '0') {
                LivrareCancelRequest::where('livrare_id', $livrare->id)->delete();
            }
            if($livrare->status >= 8) {
                // send mail with unexpected status change
            }
            $livrare->save();
        }
    }

    // For GLS courier all orders finish with status 1 (Livrata) even if the parcel was refused
    // Because of that we need to check if the order was refused before
    // and change the status to 8 manually (Returnata)
    public function getSpecialStatusForCurier(Livrare $livrare, $api_status)
    {
        $status = $this->getOrderStatusPerCurierStatus($api_status, $livrare->api);
        if($livrare->api == 3 && $status == 1 && LivrareStatus::where([
                'api' => '3',
                'api_shipment_id' => $livrare->api_shipment_id,
                // 'api_status_code' => 17
            ])->whereIn('api_status_code', [17, 23, 40])
            ->where(\DB::raw('DATE(created_at)'), '<>', function($query) use ($livrare, $api_status) {
                $query->selectRaw('DATE(created_at)')->from(LivrareStatus::getTableName())->where([
                    'api' => $livrare->api,
                    'api_shipment_id' => $livrare->api_shipment_id,
                ])->where('api_status_code', $api_status)->limit(1);
            })->exists()
        ) {
            return 8;
        }
        return $status;
    }

    public function getOrderStatusPerCurierStatus($api_status, int $api_curier = 2)
    {
        foreach($this->getCurierStatusPerType($api_curier) as $status => $api_all_status) {
            $api_status = (string)((int) $api_status);
            if(in_array($api_status, $api_all_status)) {
                return $status;
            }
        }
        return null;
    }

    // 0 => __('Neridicata'),
    // 1 => __('Livrata'),
    // 2 => __('In tranzit'),
    // 3 => __('Ajuns in depozit destinatie'),
    // 4 => __('In livrare'),
    // 5 => __('Anulata'),
    // 6 => __('Propus spre anulare'),
    // 7 => __('Ridicare din sediu'),
    // 8 => __('Returnata'),
    // 9 => __('Livrare reprogramata'),
    // 10 => __('Redirectionare'),
    // 11 => __('Adresa gresita/incompleta'),
    // 12 => __('Refuzata'),
    // 13 => __('Livrare nereusita'),
    // 14 => __('Expediere semnalata cu avarie'),
    // 15 => __('Expediere incompleta'),

    public function getCurierStatusPerType($api)
    {
        $status_curier = [
            1 => [
                1 => ['60','61','78','146','147','21'],
                2 => ['7', '11','12'],
                3 => ['9','149'],
                4 => ['70','5'],
                5 => ['99','207'],
                6 => [],
                7 => ['38','42','176'],
                8 => ['196','197','198','243'],
                9 => [
                    '68','95','96','97','100','101','102','103','104','105','106',
                    '113','114','117','118','120','121','122','123','127',
                    '131','132','133','136','138','140','141','142','145'
                ],
                10 => ['43','225','226','230'],
                11 => ['1','2','3','4','6','8','14','18'],
                12 => ['55','153','158','159','161','180','181','182','183','185','186','189'],
                13 => ['51','53','54','57','77','166','167','168','177'],
                14 => ['85','86','87','88','89','90','98','69','71'],
                15 => []
            ],
            2 => [
                // 0 => ['148'],
                1 => ['-14'],
                2 => ['2', '39'],
                3 => ['1','11'], // 11 is in both Track And Trace Operation Exception Codes for DPD
                4 => ['12'],
                5 => ['128'],
                6 => [],
                7 => ['134'],
                8 => ['111','124'],
                9 => ['69','80'], // 181
                10 => ['116','1005'],
                11 => ['1002'],
                12 => ['12','14','15','16','123','195','1006'],
                13 => ['19','35','37','38','42','44','164','1003'],  // 164 - can be in both 9 or 13 status
                14 => ['125'],
                15 => ['29']
            ],
            3 => [
                // 0 => ['1'],
                1 => ['5','05','58'],
                2 => ['2','3','6','7','26','53','86'],
                3 => ['22', '1'],
                4 => ['4', '10'],
                5 => ['91'],
                6 => [],
                7 => ['8'],
                8 => ['23','40'],
                9 => ['9','32','88'], // 181
                10 => ['46','54','55'],
                11 => ['18','20','62'],
                12 => ['17'],
                13 => ['11','12','15','16','19','29','33','34','35','36','37','38','43','44'],
                14 => ['28','30','31','42'],
                15 => ['69']
            ],
        ];
        return $status_curier[$api] ?? [];
    }
}
