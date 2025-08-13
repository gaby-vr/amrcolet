<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'content',
        'nr_colete',
        'total_weight',
        'total_volume',
        'favorite',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    protected $table = 'templates';

    
}
