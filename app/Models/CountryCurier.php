<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryCurier extends Model
{
    protected $fillable = [
        'country_id',
        'curier_id',
        'user_id',
        'ramburs', 
        'volum_price', 
        'transa_ramburs', 
        'value_ramburs', 
        'percent_ramburs'
    ];

    protected $table = 'country_curier';

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
