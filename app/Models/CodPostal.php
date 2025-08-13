<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodPostal extends Model
{
    protected $fillable = [
        'judet',
        'localitate',
        'strada',
        'de_la',
        'pana_la',
        'paritate',
        'cod_postal',
        'tip',
    ];

    protected $table = 'coduri_postale_optimizat';

    public static function getStaticFillable()
    {
        return (new CodPostal)->fillable;
    }

    public static function counties($search = null, $limit = null, $name = null)
    {
        $query = self::select('judet');
        $query = self::search($query, $search, $limit);
        return $query->orderBy($name ?? 'judet');
    }

    public static function localities($search = null, $limit = null, $name = null)
    {
        $query = self::select('localitate');
        $query = self::search($query, $search, $limit);
        return $query->orderBy($name ?? 'localitate');
    }

    public static function streets($search = null, $limit = null, $name = null)
    {
        $query = self::select('*')->where('cod_postal', '<>', '');
        $query = self::search($query, $search, $limit);
        return $query->distinct()->orderBy($name ?? 'strada');
    }

    public static function postalCodes($search = null, $limit = null, $name = null)
    {
        $query = self::select('*')->where('cod_postal', '<>', '');
        $query = self::search($query, $search, $limit);
        return $query->distinct()->orderBy($name ?? 'cod_postal');
    }

    public static function onlyPostalCode($cod_postal = null)
    {
        return $cod_postal ? self::where('cod_postal', $cod_postal)->count() == 1 : false;
    }

    public static function search($query, $conditions = null, $limit = null)
    {
        $conditions = is_array($conditions) ? $conditions : [];
        foreach ($conditions as $col => $value) {
            if(in_array($col, self::getStaticFillable()) && $value) {
                $query->where($col, 'LIKE', $value);
            }
        }
        if($limit) {
            $query->take($limit);
        }
        return $query->distinct();
    }
}
