<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait MetaTrait
{
    protected $meta_table = null;
    protected $meta_key = null;

    public function getMetaTableName() 
    {
        if($this->meta_table) {
            return $this->meta_table;
        } else {
            $this->meta_table = strtolower(class_basename(self::class)).'_metas';
            return $this->meta_table;
        }
    }

    public function getMetaKey() 
    {
        if($this->meta_key) {
            return $this->meta_key;
        } else {
            $this->meta_key = strtolower(class_basename(self::class)).'_id';
            return $this->meta_key;
        }
    }

    public function getMetaPrefix() 
    {
        return '';
    }

    public function getMetaSuffix() 
    {
        return '';
    }

    public function getMetaColumnKeyName() 
    {
        return 'name';
    }

    public function setMeta($key, $value)
    {
        if($this->meta($key) != '' && $value != '' && $value != null) {
            return $this->metas()->where($this->getMetaColumnKeyName(), $key)->update(['value' => $value, 'updated_at' => now()]);
        }
        return $value ? DB::table($this->getMetaTableName())
            ->insert([
                $this->getMetaKey() => $this->id,
                $this->getMetaColumnKeyName() => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]) : null;
    }

    public function setMetas(array $array, $prefix = null, $suffix = null)
    {
        $prefix = $prefix != null ? $prefix : $this->getMetaPrefix();
        $suffix = $suffix != null ? $suffix : $this->getMetaSuffix();
        $upsert = [];
        // array of key => value pairs
        foreach ($array as $key => $value) {
            if($value) {
                $upsert[] = [
                    $this->getMetaKey() => $this->id,
                    $this->getMetaColumnKeyName() => ($prefix ?? '').$key.($suffix ?? ''),
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        return DB::table($this->getMetaTableName())
            ->upsert($upsert, [$this->getMetaKey(), $this->getMetaColumnKeyName()], ['value', 'updated_at']);
    }

    public function unsetMeta($key, $operator = '=')
    {
        return $this->metas()->where($this->getMetaColumnKeyName(), $operator, $key)->count() > 0 
            ? $this->metas()->where($this->getMetaColumnKeyName(), $operator, $key)->delete() 
            : null;
    }

    public function unsetMetaKeys(array $keys)
    {
        return $this->metas()->whereIn($this->getMetaColumnKeyName(), $keys)->delete();
    }

    public function meta($key, $return = '')
    {
        $data = $this->metas()->where($this->getMetaColumnKeyName(), $key)->first();
        return $data ? $data->value : $return;
    }

    public function getMetas($prefix = null, $suffix = null, $order = 'asc', $keep = false)
    {
        $prefix = $prefix != null ? $prefix : $this->getMetaPrefix();
        $suffix = $suffix != null ? $suffix : $this->getMetaSuffix();
        return $this->metas()->select($this->getMetaColumnKeyName(),'value')
            ->where($this->getMetaColumnKeyName(), 'like', $prefix.'%'.$suffix)
            ->orderBy($this->getMetaColumnKeyName(), $order)
            ->get()->mapWithKeys(function ($item) use ($prefix, $suffix, $keep) {
                if($prefix && $prefix != '' && !$keep) {
                    return [explode($prefix, $item->{$this->getMetaColumnKeyName()})[1] => $item->value];
                } elseif($suffix && $suffix != '' && !$keep) {
                    return [explode($suffix, $item->{$this->getMetaColumnKeyName()})[0] => $item->value];
                }
                return [$item->{$this->getMetaColumnKeyName()} => $item->value];
            })->toArray();
    }

    public function unsetMetas($prefix = null, $suffix = null)
    {
        $prefix = $prefix != null ? $prefix : $this->getMetaPrefix();
        $suffix = $suffix != null ? $suffix : $this->getMetaSuffix();
        return $this->metas()->where($this->getMetaColumnKeyName(), 'like', $prefix.'%'.$suffix)->delete();
    }

    public function countMetas($prefix = null, $suffix = null)
    {
        $prefix = $prefix != null ? $prefix : $this->getMetaPrefix();
        $suffix = $suffix != null ? $suffix : $this->getMetaSuffix();
        return $this->metas()->select($this->getMetaColumnKeyName(),'value')
            ->where($this->getMetaColumnKeyName(), 'like', $prefix.'%'.$suffix)
            ->count();
    }

    public function metas()
    {
        return DB::table($this->getMetaTableName())->where($this->getMetaKey(), $this->id);
    }

    public function withMetas($prefix = null, $suffix = null, $order = 'asc', $keep = false)
    {
        $prefix = $prefix != null ? $prefix : $this->getMetaPrefix();
        $suffix = $suffix != null ? $suffix : $this->getMetaSuffix();
        foreach($this->getMetas($prefix, $suffix, $order, $keep) ?? [] as $key => $value) {
            $this->{$key} = $value;
            $this->original[$key] = $value;
        }
        return $this;
    }

    public function withMetaKeys(array $keys, $order = 'asc')
    {
        $metas = $this->metas()->select($this->getMetaColumnKeyName(),'value')
            ->whereIn($this->getMetaColumnKeyName(), $keys)
            ->orderBy($this->getMetaColumnKeyName(), $order)
            ->get();
        foreach($metas ?? [] as $item) {
            $this->{$item->{$this->getMetaColumnKeyName()}} = $item->value;
            $this->original[$item->{$this->getMetaColumnKeyName()}] = $item->value;
        }
        return $this;
    }

    public function withMetasToOriginal($prefix = null, $suffix = null, $order = 'asc')
    {
        $prefix = $prefix != null ? $prefix : $this->getMetaPrefix();
        $suffix = $suffix != null ? $suffix : $this->getMetaSuffix();
        foreach($this->getMetas($prefix, $suffix, $order) ?? [] as $key => $value) {
            $this->setOriginal($key, $value);
        }
        return $this;
    }

    public function withAllMetas($order = 'asc')
    {
        return $this->withMetas('', '', $order);
    }

    protected static function getMetaFields() { return []; }

    public static function rulesMetas() { return []; }

    public static function messagesMetas() { return []; }

    public static function namesMetas() { return []; }

    private $meta_fields_values = null;

    // add meta field to fillable array if it has values
    public function initializeMetaTrait()
    {
        if(count($this->fillable)) {
            $this->fillable = array_merge($this->fillable, $this->getMetaFields());
        }
    }

    public static function bootMetaTrait()
    {
        static::saving(function ($model) {
            if(method_exists($model, 'getMetaFields')) {
                // keep meta values until the 'saved' boot method was called
                $model->meta_fields_values = array_filter($model->only($model->getMetaFields()));
                // remove meta attributes before saving to avoid error
                $model->attributes = collect($model->getAttributes())->except($model->getMetaFields())->toArray();
            }
        });
        static::saved(function ($model) {
            if(method_exists($model, 'getMetaFields')) {
                // check if the fields have values and the model instance was created (has an id)
                if(!empty($model->meta_fields_values) && $model->id) {
                    $model->unsetMetas();
                    $model->setMetas($model->meta_fields_values);
                }
                // remove meta values after saving
                $model->meta_fields_values = null;
            }
        });
        static::deleted(function ($model) {
            if(method_exists($model, 'getMetaFields')) {
                $model->metas()->delete();
            }
        });
    }
}
