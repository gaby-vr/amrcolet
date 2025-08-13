<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurierDsicount extends Model
{
    protected $fillable = [
        'curier_id', 
        'nr_colete', 
        'discount'
    ];

    protected $table = 'curieri_discounts';
}
