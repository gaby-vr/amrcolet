<?php

namespace App\Models;

use App\Traits\MetaTrait;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Curier extends Model
{
    use MetaTrait;

    protected $fillable = [
        'api_curier',
        'type',
        'name',
        'logo',
        'tva', 
        'volum_price', 
        'min_6kg_price', 
        'percent_price', 
        'minim_price', 
        'max_package_weight', 
        'max_total_weight', 
        'max_order_days', 
        'performance_pickup', 
        'performance_delivery',
        'work_saturday',
        'retur_document',
        'retur_document_price',
        'require_awb',
        'ramburs_cash',
        'ramburs_cont',
        'assurance',
        'open_when_received',
        'open_when_received_price',
        'last_order_hour', 
        'last_pick_up_hour',
        'external_orders',
        'office',
        'more_information',
    ];

    protected $table = 'curieri';

    // relationships
    public function livrare()
    {
        return $this->livrare_id != 0 ? $this->belongsTo(Livrare::class) : null;
    }

    public function discounts()
    {
        return $this->hasMany(CurierDsicount::class)->orderBy('nr_colete');
    }

    public function discountsDesc()
    {
        return $this->hasMany(CurierDsicount::class)->orderByDesc('nr_colete');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'curier_user', 'curier_id', 'user_id');
    }

    public function rates($country_id = null)
    {
        return $this->hasMany(Rate::class, 'curier_id')
            ->when($country_id, fn($query) => $query->where('country_id', $country_id))
            ->whereNull('user_id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, CountryCurier::class, 'curier_id', 'country_id')
            ->withPivot('ramburs', 'volum_price', 'transa_ramburs', 'value_ramburs', 'percent_ramburs')->withTimestamps();
    }

    public function countryPrices($country_id = null)
    {
        return $this->hasMany(CountryCurier::class, 'curier_id')
            ->when($country_id, fn($query) => $query->where('country_id', $country_id))
            ->whereNull('user_id');
    }


    public function scopeExternalOrders($query, $ramburs = false)
    {
        return $query->where('external_orders', '>=', $ramburs ? 2 : 1);
    }


    //getters
    public function getPricesAttribute()
    {
        return $this->pricesMeta();
    }

    public function getVolumPriceAttribute($value)
    {
        if(auth()->check() && $price = auth()->user()->meta('special_volum_price')) {
            return $price;
            // if($price != '') {
            //     return $price;
            // } elseif ($value == null || $value == '') {
            //     return 0;
            // }
        }
        return $value;
    }

    public function getVolumPriceForUser($user = null, $country_code = null)
    {
        if($country_code && $country_code != 'ro' && $id = get_country_id($country_code)) {
            $countryPrice = $user ? $user->countryPrices($this->id, $id)->first() : $this->countryPrices($id)->first();
            $countryPrice = $countryPrice && $countryPrice->volum_price ? $countryPrice : $this->countryPrices($id)->first();
            if($countryPrice && isset($countryPrice->volum_price)) {
                return $countryPrice->volum_price;
            }
        }
        if($user && $price = ($user->import_volum_price ?? $user->meta('special_volum_price'))) {
            return $price;
        }
        return $this->volum_price;
    }

    public function getPercentAssuranceAttribute($value)
    {
        if($price = $this->meta('special_percent_assurance')) {
            return $price;
        }
        return 0;
    }

    public function getPercentRambursAttribute($value)
    {
        if($price = $this->meta('special_percent_ramburs')) {
            return $price;
        }
        return 0;
    }

    // value open when received
    public function getValueOwrAttribute($value)
    {
        if(auth()->check() && $price = auth()->user()->meta('special_value_owr')) {
            return $price;
        } elseif($price = $this->meta('special_value_owr')) {
            return $price;
        }
        return 0;
    }

    public function getValueRambursAttribute($value)
    {
        if(auth()->check() && $price = auth()->user()->meta('special_value_ramburs')) {
            return $price;
        } elseif($price = $this->meta('special_value_ramburs')) {
            return $price;
        }
        return 0;
    }

    // public function getPercentPriceAttribute($value)
    // {
    //     if(auth()->check() && auth()->user()->meta('special_percent_price') != ''){
    //         return auth()->user()->meta('special_percent_price');
    //     }
    //     return $value;
    // }

    // custom functions

    public function ratesByCountry($countryId)
    {
        $this->rates->where('country_id', $countryId);
    }

    public function addPercent($value, $percent, $fromValue = null)
    {
        $fromValue = $fromValue === null ? $value : $fromValue;
        return $value + ($fromValue * $percent/100);
    }

    public function addRambursValue($total)
    {
        return $this->value_ramburs ? ($total + $this->value_ramburs) : round($this->addPercent($total, $this->percent_ramburs), 2);
    }

    public function pricesMeta($order = 'desc')
    {
        return $this->metas()->select('name','value')->where('name', 'like', '%_kg')->orderBy('name', $order)->get()->mapWithKeys(function ($item) {
            return [(int)explode("_kg", $item->name)[0] => $item->value];
        })->sortKeysDesc()->toArray() ?? [];
    }

    public function pricesMetaWithKey($key_index, $key_value, $order = 'asc')
    {
        return $this->metas()->select('name','value')->where('name', 'like', '%_kg')->orderBy('name', $order)->get()
            ->map(function ($item) use($key_index, $key_value) {
                return [
                    $key_index => (int)explode("_kg", $item->name)[0], 
                    $key_value => $item->value
                ];
            })->sortBy('kg')->toArray();
    }

    public function discount($value)
    {
        foreach($this->discountsDesc as $discount) {
            if($value >= $discount->nr_colete) {
                return $discount->discount;
            // in case that is an envelope it always count as 1 package
            } elseif($value == 0 && 1 >= $discount->nr_colete) { 
                return $discount->discount;
            }
        }
        return 0; 
    }

    public function minimPriceForKg($weight, $user = null, $parcels = null, $import = null, $country_code = null)
    {
        if(auth()->check() || ($user != null)) {
            $user = $user ?? auth()->user();
            $price = $this->calculatePriceForKg(
                $weight, $user->pricesCurierMeta($this->id, 'desc', $import, $country_code) ?? [], 
                $this->getVolumPriceForUser($user, $country_code) ?? null,
                $country_code 
            );
        }

        if(is_array($parcels) && count($parcels) > 1 && $this->api_curier == 3) {
            $price = 0;
            foreach ($parcels as $parcel) {
                $price += $this->calculatePriceForKg(
                    $parcel['weight'] ?? 1, 
                    (!empty($user) ? ($user->pricesCurierMeta($this->id, 'desc', $import, $country_code) ?? []) : []), 
                    $this->getVolumPriceForUser($user, $country_code),
                    $country_code 
                );
            }
        }

        return $price ?? $this->calculatePriceForKg($weight, null, null, $country_code);
    }

    public function calculatePriceForKg($weight, $prices = null, $volum_price = null, $country_code = null)
    {
        if($country_code !== null && $country_code != 'ro' && $id = get_country_id($country_code)) {
            $prices = !empty($prices) ? $prices : $this->rates($id)->orderBy('weight', 'desc')->pluck('price', 'weight')->toArray();
            $volum_price = $volum_price !== null ? $volum_price : $this->getVolumPriceForUser(null, $country_code);
            if(empty($prices)) {
                return null;
            }
        }
        $prices = !empty($prices) ? $prices : $this->pricesMeta();
        $prevPrice = false;
        $prevKg = false;
        if(count($prices ?? []) > 0) {
            foreach ($prices as $kg => $price) {
                if($kg >= $weight && $kg - 1 < $weight) {
                    return $price;
                } elseif($kg < $weight && $prevPrice) {
                    return $prevPrice;
                } elseif($kg > $weight) {
                    $prevPrice = $price;
                    $prevKg = $kg;
                }
            }
            if($prevKg > $weight) {
                return $prevPrice;
            }
            $kg = array_keys($prices)[0];
            $kgInPlus = ceil($weight - $kg) < 1 ? 1 : ceil($weight - $kg);
            return array_values($prices)[0] + $kgInPlus * ($volum_price ?? $this->volum_price);
        }
        return null;
    }

    public function calculatePriceForConditions($package, $weight, $user = null, $parcels = null, $import = null, $country_code = null)
    {
        $value = $this->minimPriceForKg($weight, $user, $parcels, $import, $country_code);
        $added = isset($package['assurance']) && $package['assurance'] > 0 
            ? $package['assurance'] * $this->percent_assurance/100
            : 0;
        $price = floatval($value) + floatval($added);

        if(
            // auth()->check() && auth()->id() == 1 &&
            isset($package['ramburs']) && $package['ramburs'] == '3'
            && isset($package['ramburs_value']) && $package['ramburs_value'] > 0
        ) {
            $price = $this->addRambursValue($price);
            if($country_code && $country_code !== 'ro' && $id = get_country_id($country_code)) {
                $countryPriceUser = $user ? $user->countryPrices($this->id, $id)->first() : null;
                $countryPriceCurier = $this->countryPrices($id)->first();

                $transa = $countryPriceUser && $countryPriceUser->transa_ramburs 
                    ? $countryPriceUser->transa_ramburs 
                    : ($countryPriceCurier && $countryPriceCurier->transa_ramburs ? $countryPriceCurier->transa_ramburs : null);

                $adaosFix = $countryPriceUser && $countryPriceUser->value_ramburs 
                    ? $countryPriceUser->value_ramburs 
                    : ($countryPriceCurier && $countryPriceCurier->value_ramburs ? $countryPriceCurier->value_ramburs : 0);

                $procentFix = $countryPriceUser && $countryPriceUser->percent_ramburs 
                    ? $countryPriceUser->percent_ramburs 
                    : ($countryPriceCurier && $countryPriceCurier->percent_ramburs ? $countryPriceCurier->percent_ramburs : 0);

                if($transa === null) {
                    return null;
                }
                $price = $package['ramburs_value'] <= $transa ? ($price + $adaosFix) : round($this->addPercent($price, $procentFix, $package['ramburs_value']), 2);
            }
        }
        if(isset($package['options']['open_when_received']) && $package['options']['open_when_received'] > 0) {
            $price += $this->value_owr;
        }
        if(isset($package['options']['retur_document']) && $package['options']['retur_document'] > 0) {
            $price += $this->minimPriceForKg($package['swap_details']['total_weight'] ?? 1, $user, null, $import, $country_code);
        }
        return $price;
    }

    protected static function booted(): void
    {
        // prevent scopes in admin
        if(!request()->route() || !in_array('auth.admin', request()->route()->gatherMiddleware() ?? [])) {
            // static::addGlobalScope('only_admin', function ($builder) {
            //     if(!auth()->check() || !auth()->user()->is_admin) {
            //         $builder->where('api_curier', '<>', 3);
            //     }
            // });

            static::addGlobalScope('inactive', function ($builder) {
                $builder->where('type', '<>', 3);
            });
            static::addGlobalScope('active', function ($builder) {
                $builder->where(function($query) {
                    $query->where('type', 1);
                    if(auth()->check()) {
                        $query->orWhere('type', 2)->whereExists(function ($query) {
                            $query->select(\DB::raw(1))->from('curier_user')->where('user_id', auth()->id());
                        });
                        // $query->orWhere('type', 2)->whereHas('users', function ($query) {
                        //     $query->where('users.id', auth()->id());
                        // });
                    }
                });
            });
        }
    }

    /*public function minimPriceForKg_old($weight, $user = null)
    {
        if(auth()->check() || ($user != null)) {
            $user = auth()->user() ?? $user;
            $prices = $user->pricesCurierMeta($this->id) ?? [];
            $prevPrice = false;
            $prevKg = false;
            if(count($prices) > 0) {
                foreach ($prices as $kg => $price) {
                    if($kg >= $weight && $kg - 1 < $weight) {
                        return $price;
                    } elseif($kg < $weight && $prevPrice) {
                        return $prevPrice;
                    } elseif($kg > $weight) {
                        $prevPrice = $price;
                        $prevKg = $kg;
                    }
                }
                if($prevKg > $weight) {
                    return $prevPrice;
                }
                $kg = array_keys($prices)[0];
                $kgInPlus = ceil($weight - $kg) < 1 ? 1 : ceil($weight - $kg);
                return array_values($prices)[0] + $kgInPlus * $this->volum_price;
            }
        }
        $prices = $this->pricesMeta();
        $prevPrice = false;
        $prevKg = false;
        if(count($prices) > 0) {
            foreach ($prices as $kg => $price) {
                if($kg >= $weight && $kg - 1 < $weight) {
                    return $price;
                } elseif($kg < $weight && $prevPrice) {
                    return $prevPrice;
                } elseif($kg > $weight) {
                    $prevPrice = $price;
                    $prevKg = $kg;
                }
            }
            if($prevKg > $weight) {
                return $prevPrice;
            }
            $kg = array_keys($prices)[0];
            $kgInPlus = ceil($weight - $kg) < 1 ? 1 : ceil($weight - $kg);
            return array_values($prices)[0] + $kgInPlus * $this->volum_price;
        }
        return null;
    }*/
}
