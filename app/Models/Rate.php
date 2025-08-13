<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'price',
        'weight',
        'country_id',
        'curier_id',
        'user_id',
    ];

    protected $table = 'rates';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function curier()
    {
        return $this->belongsTo(Curier::class, 'curier_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
