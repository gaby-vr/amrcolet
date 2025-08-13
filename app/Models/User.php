<?php

namespace App\Models;

use App\Traits\MetaTrait;
use App\Traits\OrderValidationTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, MetaTrait, OrderValidationTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function favoriteAddresses()
    {
        return $this->hasMany(Address::class)->orderByDesc('favorite')->orderBy('address_name');
    }

    public function favoriteTemplates()
    {
        return $this->hasMany(Template::class)->orderByDesc('favorite')->orderBy('name');
    }

    public function orders()
    {
        return $this->hasMany(Livrare::class);
    }

    public function livrari()
    {
        return $this->orders();
    }

    public function couriers()
    {
        return $this->belongsToMany(Curier::class, 'curier_user', 'user_id', 'curier_id');
    }

    public function unpayedOrders()
    {
        return $this->orders()->where('payed','0')->whereNotIn('status', ['5', '4']);
    }

    public function borderouri()
    {
        return $this->hasMany(Borderou::class);
    }

    public function rates($curier_id = null, $country_id = null)
    {
        return $this->hasMany(Rate::class, 'user_id')
            ->when($curier_id, fn($query) => $query->where('curier_id', $curier_id))
            ->when($country_id, fn($query) => $query->where('country_id', $country_id));
    }
    
    public function countryPrices($curier_id = null, $country_id = null)
    {
        return $this->hasMany(CountryCurier::class, 'user_id')
            ->when($curier_id, fn($query) => $query->where('curier_id', $curier_id))
            ->when($country_id, fn($query) => $query->where('country_id', $country_id));
    }

    public function borderouAwbs()
    {
        return $this->hasManyThrough(BorderouAwb::class, Borderou::class);
    }

    public function lastBorderou()
    {
        return $this->hasOne(Borderou::class)->whereNull('payed_at')->orderByDesc('end_date');
    }

    public function invoiceSheets()
    {
        return $this->hasMany(InvoiceSheet::class);
    }

    public function sheetAwbs()
    {
        return $this->hasManyThrough(InvoiceSheetAwb::class, InvoiceSheet::class, 'user_id', 'sheet_id');
    }

    public function lastInvoiceSheet()
    {
        return $this->hasOne(InvoiceSheet::class)->orderByDesc('id');
    }

    public function invoiceInfo()
    {
        return $this->metas()->select('name','value')->where('name', 'like', 'invoice_%')->get()->mapWithKeys(function ($item) {
            return [explode("invoice_", $item->name)[1] => $item->value];
        })->toArray();
    }

    public function invoiceInfoCount()
    {
        return $this->metas()->select('name','value')->where('name', 'like', 'invoice_%')->count();
    }

    public function withBalance()
    {
        return $this->withMetaKeys([
            'account_balance',
            'bonus_credits',
            'days_of_negative_balance',
            'expiration_date',
        ]);
    }

    public function pricesCurierMeta($curier_id, $order = 'desc', $import = null, $country_code = null)
    {
        if($import) {
            return $this->import_prices;
        }
        if($country_code && $country_code !== 'ro' && $id = get_country_id($country_code)) {
            // if(auth()->id() == 1) {
                return $this->rates($curier_id, $id)->orderBy('weight', $order)->pluck('price', 'weight')->toArray();
            // }
            // $result = $this->results
        }
        return $this->metas()->select('name','value')->where('name', 'like', '%_kg_'.$curier_id)->orderBy('name', $order)->get()
            ->mapWithKeys(function ($item) use ($curier_id) {
                return [explode("_kg_".$curier_id, $item->name)[0] => $item->value];
            })->toArray();
    }

    public function pricesMetaWithKey($key_index, $key_value, $curier_id, $order = 'asc', $import = null, $country_code = null)
    {
        if($import) {
            return $this->import_prices;
        }
        return $this->metas()->select('name','value')->where('name', 'like', '%_kg_'.$curier_id)->orderBy('name', $order)->get()
            ->map(function ($item) use($key_index, $key_value, $curier_id) {
                return [
                    $key_index => explode("_kg_".$curier_id, $item->name)[0], 
                    $key_value => $item->value
                ];
            })->toArray();
    }

    public function getAnnouncementSeenAttribute()
    {
        return $this->meta('announcement_seen');
    }

    // public static function bankCodes()
    // {
    //     return [
    //         'ABNA', 'ARBL', 
    //         'BCUN', 'BCYP', 'BITR', 'BLOM', 'BPOS', 'BRDE', 'BREL', 'BRMA', 'BSEA', 'BTRL', 'BUCU', 'BCRL', 'BACX',
    //         'CAIX', 'CARP', 'CECE', 'CITI', 'CRCO', 'CRDZ', 
    //         'DABA', 'DAFB', 'DARO', 'DPFA', 
    //         'EGNA', 'ETHN', 'EXIM', 
    //         'FNNB', 'FRBU', 'FTSB', 
    //         'HVBL', 
    //         'INGB', 
    //         'MILB', 'MIND', 'MIRO', 
    //         'NBOR', 
    //         'OTPV', 
    //         'PIRB', 'PORL', 
    //         'RNCB', 'ROIN', 'RZBL', 'RZBR', 
    //         'TRFD', 
    //         'UGBI', 
    //         'VBBU', 
    //         'WBAN', 
    //         'TREZ'
    //     ];
    // }
}
