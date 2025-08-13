<?php

namespace App\Models;

use App\Traits\ConversionTrait;
use App\Traits\MetaTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use MetaTrait, ConversionTrait;

    protected $fillable = [
        'user_id',
        'series', 
        'number', 
        'status', 
        'total', 
        'payed_on',
        'credited_by',
        'external_link',
        'spv',
    ];

    protected $appends = ['livrare_id'];

    public function scopeByAdmin($query)
    {
        return $query->whereIn('id', function($subquery) {
            $subquery->select($this->getMetaKey())
                ->from($this->getMetaTableName())
                ->where($this->getMetaColumnKeyName(), 'created_by_admin');
        });
    }

    public function scopeNotByAdmin($query)
    {
        return $query->whereIn('id', function($subquery) {
            $subquery->select($this->getMetaKey())
                ->from($this->getMetaTableName())
                ->where($this->getMetaColumnKeyName(), '<>', 'created_by_admin')
                ->groupBy($this->getMetaKey());
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livrare()
    {
        return $this->hasOne(Livrare::class, 'invoice_id');
    }

    public function livrari()
    {
        return $this->hasMany(Livrare::class, 'invoice_id');
    }

    public function storned()
    {
        return $this->hasOne(Invoice::class, 'credited_by');
    }

    public function storno()
    {
        return $this->hasOne(Invoice::class, 'id', 'credited_by');
    }



    public function getNumberAttribute($value)
    {
        return str_pad($value,  4, "0", STR_PAD_LEFT);
    }

    public function getClientFullNameAttribute($value)
    {
        return $this->client_last_name.' '.$this->client_first_name;
    }

    public function getLivrareIdAttribute()
    {
        return $this->livrare->id ?? $this->storned->livrare->id ?? null;
    }

    public function getStatusTextAttribute()
    {
        return static::statusText($this->status);
    }

    public function getMetasAttribute()
    {
        return collect($this->getMetas());
    }

    public function getClientAttribute()
    {
        $address = $this->metas['client_address'] ?? '';
        return $this->metas->filter(function ($item, $key) {
            return count(explode('client_', $key)) > 1;
        })->mapWithKeys(function ($item, $key) {
            return [explode('client_', $key)[1] => $item];
        })->merge([
            'street' => get_string_between($address, 'Str. ', ' Nr.'),
            'street_nr' => get_string_between($address, 'Nr. ', ', '),
            'bl_code' => get_string_between($address, 'Bl. ', ', '),
            'bl_letter' => get_string_between($address, 'Sc. ', ', '),
            'intercom' => get_string_between($address, 'Interfon ', ', '),
            'floor' => get_string_between($address, 'Etaj ', ', '),
            'apartment' => get_string_between($address, 'Ap./Nr. ', ', '),
        ])->toArray();
    }

    public function getProviderAttribute()
    {
        return $this->metas->filter(function ($item, $key) {
            return count(explode('provider_', $key)) > 1;
        })->mapWithKeys(function ($item, $key) {
            return [explode('provider_', $key)[1] => $item];
        })->toArray();
    }

    public function getProductsAttribute()
    {
        return $this->metas->filter(function ($item, $key) {
            $parts = explode('_', $key);
            return count(explode('product_', $key)) > 1 
                && count($parts) === 3 && $parts[2] == (int)$parts[2];
        })->mapWithKeys(function ($item, $key) {
            $new_key = explode('_', $key);
            return [$new_key[2].'.'.$new_key[1] => $item];
        })->undot()->sortKeys()->toArray();
    }

    public static function statusText($status)
    {
        $status = static::statusList($status);
        return !is_array($status) ? $status : '';
    }

    public static function statusList($status = null)
    {
        $list = [
            0 => __('Plata in asteptare'),
            1 => __('Confirmata'),
            2 => __('Anulata'),
            3 => __('Stornata'),
            4 => __('Respinsa'),
        ];
        return $status !== null ? ( $list[$status] ?? null ) : $list;
    }
}
