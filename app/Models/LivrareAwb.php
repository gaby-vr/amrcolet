<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivrareAwb extends Model
{
    protected $fillable = [
        'api',
        'api_awb',
        'parcel_list',
        'parcel_awb_list',
        'awb',
    ];

    protected $casts = [
        'parcel_list' => 'array',
        'parcel_awb_list' => 'array',
    ];

    protected $table = 'livrari_awbs';
}
