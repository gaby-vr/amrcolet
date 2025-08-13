<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Contact extends Model
{
    protected $fillable = [
        'livrare_id',
        'type',
        'name',  
        'phone', 
        'phone_2', 
        'email', 
        'company', 
        'country',
        'country_code', 
        'postcode', 
        'county',
        'locality',  
        'street',
        'street_nr',
        'apartment',
        'bl_code',
        'bl_letter',  
        'intercom',   
        'floor',  
        'landmark',    
        'more_information',
    ];

    protected $table = 'contacts';

    public function replaceBrackets($string)
    {
        return preg_replace('/\s?\(.*\)/', '', $string);
    }

    // protected function locality(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn (string $value) => $this->replaceBrackets($value),
    //     );
    // }
}
