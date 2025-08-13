<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class BorderouAwb extends Model
{
    protected $fillable = [
        'borderou_id', 
        'awb', 
        'sender_name',
        'receiver_name',
        'order_created_at',
        'payment',
        'iban',
        'account_owner',    // titular cont
    ];

    protected $table = 'borderouri_awbs';

    public function borderou()
    {
        return $this->belongsTo(Borderou::class, 'borderou_id');
    }

    public static function getTableName()
    {
        return (new static)->getTable();
    }

    public function scopeForExport($query)
    {
        return $query->select(...array_diff($this->fillable, ['borderou_id']));
    }

    public static function rules($full = false, $id = null)
    {
        return [
            'awb' => ['required', 'string', 'min:1', 'max:255', 'distinct', Rule::unique(static::class, 'awb')->ignore($id, 'borderou_id')],
            'sender_name' => ['required', 'string', 'min:1', 'max:255'],
            'receiver_name' => ['required', 'string', 'min:1', 'max:255'],
            'order_created_at' => ['required', 'date', 'after:2000-01-01'],
            'payment' => ['required', 'numeric', 'min:0', 'regex:/^-?[0-9]+(?:\\.[0-9]{1,2})?$/'],
            'iban' => ['required', 'string', 'min:24', 'max:24', 
                function ($attribute, $value, $fail) {
                    if (!preg_match('/RO[0-9]{2}('.implode('|', User::bankCodes()).')[A-Z0-9]{16}/', $value)) {
                        $fail(__('Este necesar un cont al unei banci romanesti.'));
                    }
                },
            ],
            'account_owner' => ['required', 'string', 'min:3', 'max:100'],
        ] + ($full ? [
            'borderou_id' => ['required', 'integer', 'min:1', Rule::exists(Borderou::class, 'id')],
        ] : []);
    }

    public static function names()
    {
        return [
            'borderou_id' => __('Borderou'), 
            'awb' => __('AWB'),
            'sender_name' => __('Nume expeditor'),
            'receiver_name' => __('Nume destinatar'),
            'order_created_at' => __('Data creare livrare'),
            'payment' => __('Valoare'),
            'iban' => __('IBAN'),
            'account_owner' => __('Titular cont'),
        ];
    }
}
