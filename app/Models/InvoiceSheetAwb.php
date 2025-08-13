<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class InvoiceSheetAwb extends Model
{
    protected $fillable = [
        'sheet_id', 
        'awb',
        'optional_product',
        'sender_name',
        'receiver_name',
        'order_created_at',
        'payment',
    ];

    protected $table = 'invoice_sheet_awbs';

    public function sheet()
    {
        return $this->belongsTo(InvoiceSheet::class, 'sheet_id');
    }

    public function order()
    {
        return $this->belongsTo(Livrare::class, 'awb', 'api_shipment_awb');
    }

    public static function getTableName()
    {
        return (new static)->getTable();
    }

    public function scopeForExport($query)
    {
        return $query->select(...array_diff($this->fillable, ['sheet_id']));
    }

    public function getStatusTextAttribute()
    {
        return $this->order ? $this->order->status_text : null;
    }

    public function getStatusColorAttribute()
    {
        return $this->order ? $this->order->status_color : null;
    }

    public static function rules($full = false, $id = null, $prefix = null)
    {
        return [
            $prefix.'awb' => ['nullable', 'required_if:'.$prefix.'optional_product,null','exclude_if:'.$prefix.'awb,null', 'string', 'min:1', 'max:255', 'distinct', 
                Rule::unique(static::class, 'awb')->ignore($id, 'sheet_id')
            ],
            $prefix.'optional_product' => ['nullable', 'required_if:'.$prefix.'awb,null','exclude_unless:'.$prefix.'awb,null', 'string', 'min:1', 'max:1000'],
            $prefix.'sender_name' => ['required', 'string', 'min:1', 'max:255'],
            $prefix.'receiver_name' => ['required', 'string', 'min:1', 'max:255'],
            $prefix.'order_created_at' => ['required', 'date', 'after:2000-01-01'],
            $prefix.'payment' => ['required', 'numeric', 'min:0', 'regex:/^-?[0-9]+(?:\\.[0-9]{1,2})?$/'],
        ] + ($full ? [
            $prefix.'sheet_id' => ['required', 'integer', 'min:1', Rule::exists(InvoiceSheet::class, 'id')],
        ] : []);
    }

    public static function names()
    {
        return [
            'sheet_id' => __('Fisa factura'), 
            'awb' => __('AWB'),
            'optional_product' => __('Produs optional'),
            'sender_name' => __('Nume expeditor'),
            'receiver_name' => __('Nume destinatar'),
            'order_created_at' => __('Data creare livrare'),
            'payment' => __('Valoare'),
        ];
    }
}
