<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceMeta extends Model
{
    protected $fillable = [
        'invoice_id', 'name', 'value'
    ];
}
