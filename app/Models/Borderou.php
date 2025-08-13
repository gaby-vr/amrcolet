<?php

namespace App\Models;

use App\Traits\ConversionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Borderou extends Model
{
    use ConversionTrait;

    protected $fillable = [
        'user_id', 
        'total',
        'start_date',
        'end_date',
        'payed_at',
        'exclude',
    ];

    protected $table = 'borderouri';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function borderouAwbs()
    {
        return $this->hasMany(BorderouAwb::class, 'borderou_id')->orderBy('order_created_at');
    }

    public function borderouApiRequests()
    {
        return $this->hasMany(BorderouApiRequest::class, 'borderou_id');
    }

    public function repayments()
    {
        return $this->hasManyThrough(Repayment::class, BorderouAwb::class, 'borderou_id', 'awb', 'id', 'awb');
    }

    public function scopePayed($query)
    {
        return $query->whereNotNull('payed_at');
    }

    public function scopeNotExcluded($query)
    {
        return $query->whereNull('exclude');
    }

    public static function getTableName()
    {
        return (new static)->getTable();
    }

    public static function rules($full = false)
    {
        return [
            'user_id' => ['required', 'integer', 'min:1', 
                // Rule::exists(User::class, 'id')->where(function($query) {
                //     return $query->where('role', 2);
                // }),
            ], 
            'start_date' => ['required', 'date', 'after:2000-01-01'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'payed_at' => ['nullable', 'date', 'after_or_equal:start_date'],
        ] + ($full ? [
            'total' => ['required', 'numeric', 'min:0', 'regex:/^-?[0-9]+(?:\\.[0-9]{1,2})?$/'],
        ] : []);
    }

    public static function names()
    {
        return [
            'user_id' => __('Utilizator'), 
            'total' => __('Total'),
            'start_date' => __('Data de inceput'),
            'end_date' => __('Data de sfarsit'),
            'payed_at' => __('Data platii'),
        ];
    }
}
