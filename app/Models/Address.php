<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'favorite',
        'address_name',
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

    protected $table = 'addresses';

    
}
