<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso',
        'prefix',
        'standard'
    ];

    protected $table = 'countries';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function currency()
    {
        return $this->belongsToMany(Currency::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Local scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDefault($query)
    {
        $query->where('standard', 1);
    }

    public function scopeSpecial($query)
    {
        $query->where('standard', '<>', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors and Mutators
    |--------------------------------------------------------------------------
    */

    public function getCurrencyCodeAttribute()
    {
        return $this->currency && $this->currency->first() ? $this->currency->first()->code : 'RON';
    }

    /*
    |--------------------------------------------------------------------------
    | Custom functions
    |--------------------------------------------------------------------------
    */

    public static function code($code)
    {
        return static::firstWhere('iso', $code);
    }
}