<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $fillable = [
        'user_id',
        'awb',
        'type',
        'ref_client',
        'date_order',
        'date_delivered',
        'payer_name',
        'payer_address',
        'titular_cont',
        'iban',
        'status',
        'payed_on',
        'total',
        'series_receipt',
        'date_receipt', 
    ];

    protected $table = 'repayments';

    public function livrare()
    {
        return $this->belongsTo(Livrare::class, 'awb', 'api_shipment_awb');
    }
}
