<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'livrare_id',
        'template_id',
        'weight',
        'width',
        'length',
        'height',
        'volume', 
    ];

    protected $table = 'packages';

    
}
