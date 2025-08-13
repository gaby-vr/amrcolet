<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorderouApiRequest extends Model
{
    protected $fillable = [
        'guid', 
        'borderou_id', 
        'iban',
        'account_owner',    // titular cont
        'value',
        'status',
        'header',
        'body',
        'response',
        'payment_id',
    ];

    protected $table = 'borderouri_api_requests';

    public function borderou()
    {
        return $this->belongsTo(Borderou::class, 'borderou_id');
    }

    // public static function rules($full = false)
    // {
    //     return [
    //         'value' => ['required', 'numeric', 'min:0', 'regex:/^-?[0-9]+(?:\\.[0-9]{1,2})?$/'],
    //         'iban' => ['required', 'string', 'min:24', 'max:24', 
    //             function ($attribute, $value, $fail) {
    //                 if (!preg_match('/RO[0-9]{2}('.implode('|', User::bankCodes()).')[A-Z0-9]{16}/', $value)) {
    //                     $fail(__('Este necesar un cont al unei banci romanesti.'));
    //                 }
    //             },
    //         ],
    //         'account_owner' => ['required', 'string', 'min:3', 'max:100'],
    //     ] + ($full ? [
    //         'borderou_id' => ['required', 'integer', 'min:1', Rule::exists(Borderou::class, 'id')],
    //     ] : []);
    // }

    // public static function names()
    // {
    //     return [
    //         'borderou_id' => __('Borderou'), 
    //         'value' => __('Valoare'),
    //         'iban' => __('IBAN'),
    //         'account_owner' => __('Titular cont'),
    //     ];
    // }
}
