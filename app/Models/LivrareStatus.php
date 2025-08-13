<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivrareStatus extends Model
{
    protected $fillable = [
        'api',
        'api_shipment_id',   
        'api_parcel_id',
        'api_status_code',
        'description',
    ];

    protected $table = 'livrari_status_api';

    public static function getTableName()
    {
        return (new static)->getTable();
    }
}
