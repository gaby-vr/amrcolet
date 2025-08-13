<?php
namespace App\Traits;

use Carbon\Carbon;

trait ConversionTrait
{
    // public function transformDate($column, $format = 'd/m/Y')
    // {
    //     return $this->$column ? Carbon::parse($this->$column)->format($format) : null;
    // }

    public function transformDate($column, $format = 'd/m/Y', $format_type = 'php', $from_format = null)
    {
        $function = $format_type == 'php' ? 'format' : 'isoFormat';
        $value = $this->$column ?? $column;
        if($from_format) {
            return $value && $this->isValidDate($value, $from_format) ? Carbon::createFromFormat($from_format, $value)->{$function}($format) : null;
        } else {
            return $value && $this->isValidDate($value) ? Carbon::parse($value)->{$function}($format) : null;
        }
    }
    function isValidDate($string, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $string);
        return $d && $d->format($format) == $string;
    }

    public function transformNumber($column)
    {
        if($this->$column && is_numeric($this->$column)) {
            // remove trailing 0
            $value = $this->$column + 0;
            return number_format($value , $this->getNrOfDecimals($value));
        } else {
            return null;
        } 
    }
    
    public function getNrOfDecimals($value)
    {
        // presume that the number is well formatted
        // and has only one '.' if it has decimals
        $tmp = explode('.', $value);
        // if the value has decimals
        // return the length of the second items in the array
        // which represents the decimals
        return count($tmp) > 1 ? strlen($tmp[1]) : 0;
    }
}