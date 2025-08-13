<?php 

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setare')) {
    function setare($name) {
        $setare = Setting::firstWhere('name', $name);
        return $setare ? $setare->value : '';
    }
}
if (! function_exists('setari')) {
    function setari($array, $like = false, $prefix = false, $sufix = false) {
        $names = is_array($array) ? $array : [$array];
        if($like) {
            $prefix = $prefix ? '%' : '';
            $sufix = $sufix ? '%' : '';
            $query = Setting::where('name', 'like', $prefix.$names[0].$sufix);
            unset($names[0]);
            foreach($names as $name) {
                $query->orWhere('name', 'like', $prefix.$name.$sufix);
            }
            return $query->get()->mapWithKeys(function ($item) {
                return [$item->name => $item->value];
            })->toArray();
        } else {
            return Setting::whereIn('name', $names)->get()->mapWithKeys(function ($item) {
                return [$item->name => $item->value];
            })->toArray() + map($names);
        }
    }

    if (! function_exists('map')) {
        function map($array) {
            foreach($array as $value) {
                $temp[$value] = '';
            }
            return $temp ?? [];
        }
    }
}
if(! function_exists('get_string_between')) {
    function get_string_between($string, $start, $end)
    {
        return explode($end, explode($start, $string)[1] ?? '')[0] ?? '';
    }
}
if(! function_exists('get_country_id')) {
    function get_country_id($country_code = 'ro')
    {
        $c = get_country_from_code($country_code);
        return $c ? $c->id : null;
    }
}
if(! function_exists('get_country_from_code')) {
    function get_country_from_code($country_code = 'ro')
    {
        $countries = Cache::remember('countries', 86400, function() {
            return \App\Models\Country::all()->keyBy('iso');
        });
        return $countries[strtoupper($country_code)] ?? null;
    }
}
if(! function_exists('flip_array_keys')) {
    function flip_array_keys(array $array)
    {
        $result = [];
        foreach ($array as $key => $values) {
            if(!is_array($values)) {
                return $array;
            } else {
                foreach($values as $key2 => $value) {
                    $result[$key2][$key] = $value;
                }
            }
        }
        return $result;
    }
}
if (!function_exists('input_name_to_dot')) {
    function input_name_to_dot($string, $glue = '.')
    {
        return str_replace(['[]','[',']'],[$glue.'*',$glue,''], $string);
    }
}
if (! function_exists('replace_csrf_placeholder')) {
    function replace_csrf_placeholder($string) {
        return str_replace(['CSRF_PLACEHOLDER'], [csrf_token()], $string);
    }
}
if(!function_exists('get_cursuri_valutare_array')) {
    function get_cursuri_valutare_array()
    {
        return cache()->remember('cursuri-valutare.'.date('Y-m-d'), 3600 * 24, function() {
            try {
                if($rate = simplexml_load_file('https://www.bnr.ro/nbrfxrates.xml')) {
                    $rate = $rate->Body->Cube->Rate;
                    if(!empty($rate)) {
                        $diminuare = 0.96;  // scadem cu 6% rata - pentru platforma
                        $cursuriValutare = [
                            'EUR' => (json_decode(json_encode($rate[10]), true)[0] + 0) * $diminuare,
                            'BGN' => (json_decode(json_encode($rate[2]), true)[0] + 0) * $diminuare,
                            'CZK' => (json_decode(json_encode($rate[7]), true)[0] + 0) * $diminuare,
                            'DKK' => (json_decode(json_encode($rate[8]), true)[0] + 0) * $diminuare,
                            'HUF' => round(json_decode(json_encode($rate[13]), true)[0]/100, 4) * $diminuare,
                            'PLN' => (json_decode(json_encode($rate[26]), true)[0] + 0) * $diminuare,
                            'SEK' => (json_decode(json_encode($rate[29]), true)[0] + 0) * $diminuare,
                            'GBP' => (json_decode(json_encode($rate[11]), true)[0] + 0) * $diminuare,
                            'USD' => (json_decode(json_encode($rate[34]), true)[0] + 0) * $diminuare,
                            'CHF' => (json_decode(json_encode($rate[5]), true)[0] + 0) * $diminuare,
                        ];
                        $cursuri = [];
                        foreach ($cursuriValutare as $code => $rate) {
                            $cursuri[] = [
                                'code' => $code,
                                'rate' => $rate,
                                'number' => 1 // necesary if columns with different unique values exist
                            ];
                        }
                        // dd($cursuri, !empty($cursuri));
                        if(!empty($cursuri)) {
                            \App\Models\Currency::upsert($cursuri, uniqueBy: ['code'], update: ['rate']);
                        }
                        return $cursuriValutare ?? \App\Models\Currency::pluck('rate', 'code')->toArray();
                    }
                }
            } catch(\Exception $e) {
                // \Log::info($e);
            }
            return $cursuriValutare ?? \App\Models\Currency::pluck('rate', 'code')->toArray();
        });
        // pentru buy/sell: https://api.bcr.ro/api/v1/bcr/external/getExchangeRates
    }
}
if(!function_exists('get_curs_valutar')) {
    function get_curs_valutar($key = null)
    {
        $currencies = get_cursuri_valutare_array() ?? [];
        return $currencies[$key] ?? null;
    }
}

if (! function_exists('remove_accents')) {
    function remove_accents( $string, $locale = '' ) {
        if ( ! preg_match( '/[\x80-\xff]/', $string ) ) {
            return $string;
        }
     
        if ( seems_utf8( $string ) ) {
            $chars = array(
                // Decompositions for Latin-1 Supplement.
                'ª' => 'a','º' => 'o','À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A','Æ' => 'AE',
                'Ç' => 'C','È' => 'E','É' => 'E','Ê' => 'E','Ë' => 'E','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I',
                'Ð' => 'D','Ñ' => 'N','Ò' => 'O','Ó' => 'O','Ô' => 'O','Õ' => 'O','Ö' => 'O','Ù' => 'U','Ú' => 'U',
                'Û' => 'U','Ü' => 'U','Ý' => 'Y','Þ' => 'TH','ß' => 's','à' => 'a','á' => 'a','â' => 'a','ã' => 'a',
                'ä' => 'a','å' => 'a','æ' => 'ae','ç' => 'c','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e','ì' => 'i',
                'í' => 'i','î' => 'i','ï' => 'i','ð' => 'd','ñ' => 'n','ò' => 'o','ó' => 'o','ô' => 'o','õ' => 'o',
                'ö' => 'o','ø' => 'o','ù' => 'u','ú' => 'u','û' => 'u','ü' => 'u','ý' => 'y','þ' => 'th','ÿ' => 'y',
                'Ø' => 'O',
                // Decompositions for Latin Extended-A.
                'Ā' => 'A','ā' => 'a','Ă' => 'A','ă' => 'a','Ą' => 'A','ą' => 'a','Ć' => 'C','ć' => 'c','Ĉ' => 'C',
                'ĉ' => 'c','Ċ' => 'C','ċ' => 'c','Č' => 'C','č' => 'c','Ď' => 'D','ď' => 'd','Đ' => 'D','đ' => 'd',
                'Ē' => 'E','ē' => 'e','Ĕ' => 'E','ĕ' => 'e','Ė' => 'E','ė' => 'e','Ę' => 'E','ę' => 'e','Ě' => 'E',
                'ě' => 'e','Ĝ' => 'G','ĝ' => 'g','Ğ' => 'G','ğ' => 'g','Ġ' => 'G','ġ' => 'g','Ģ' => 'G','ģ' => 'g',
                'Ĥ' => 'H','ĥ' => 'h','Ħ' => 'H','ħ' => 'h','Ĩ' => 'I','ĩ' => 'i','Ī' => 'I','ī' => 'i','Ĭ' => 'I',
                'ĭ' => 'i','Į' => 'I','į' => 'i','İ' => 'I','ı' => 'i','Ĳ' => 'IJ','ĳ' => 'ij','Ĵ' => 'J','ĵ' => 'j',
                'Ķ' => 'K','ķ' => 'k','ĸ' => 'k','Ĺ' => 'L','ĺ' => 'l','Ļ' => 'L','ļ' => 'l','Ľ' => 'L','ľ' => 'l',
                'Ŀ' => 'L','ŀ' => 'l','Ł' => 'L','ł' => 'l','Ń' => 'N','ń' => 'n','Ņ' => 'N','ņ' => 'n','Ň' => 'N',
                'ň' => 'n','ŉ' => 'n','Ŋ' => 'N','ŋ' => 'n','Ō' => 'O','ō' => 'o','Ŏ' => 'O','ŏ' => 'o','Ő' => 'O',
                'ő' => 'o','Œ' => 'OE','œ' => 'oe','Ŕ' => 'R','ŕ' => 'r','Ŗ' => 'R','ŗ' => 'r','Ř' => 'R','ř' => 'r',
                'Ś' => 'S','ś' => 's','Ŝ' => 'S','ŝ' => 's','Ş' => 'S','ş' => 's','Š' => 'S','š' => 's','Ţ' => 'T',
                'ţ' => 't','Ť' => 'T','ť' => 't','Ŧ' => 'T','ŧ' => 't','Ũ' => 'U','ũ' => 'u','Ū' => 'U','ū' => 'u',
                'Ŭ' => 'U','ŭ' => 'u','Ů' => 'U','ů' => 'u','Ű' => 'U','ű' => 'u','Ų' => 'U','ų' => 'u','Ŵ' => 'W',
                'ŵ' => 'w','Ŷ' => 'Y','ŷ' => 'y','Ÿ' => 'Y','Ź' => 'Z','ź' => 'z','Ż' => 'Z','ż' => 'z','Ž' => 'Z',
                'ž' => 'z','ſ' => 's',
                // Decompositions for Latin Extended-B.
                'Ș' => 'S','ș' => 's','Ț' => 'T','ț' => 't',
                // Euro sign.
                '€' => 'E',
                // GBP (Pound) sign.
                '£' => '',
                // Vowels with diacritic (Vietnamese).
                // Unmarked.
                'Ơ' => 'O','ơ' => 'o','Ư' => 'U','ư' => 'u',
                // Grave accent.
                'Ầ' => 'A','ầ' => 'a','Ằ' => 'A','ằ' => 'a','Ề' => 'E','ề' => 'e','Ồ' => 'O','ồ' => 'o','Ờ' => 'O',
                'ờ' => 'o','Ừ' => 'U','ừ' => 'u','Ỳ' => 'Y','ỳ' => 'y',
                // Hook.
                'Ả' => 'A','ả' => 'a','Ẩ' => 'A','ẩ' => 'a','Ẳ' => 'A','ẳ' => 'a','Ẻ' => 'E','ẻ' => 'e','Ể' => 'E',
                'ể' => 'e','Ỉ' => 'I','ỉ' => 'i','Ỏ' => 'O','ỏ' => 'o','Ổ' => 'O','ổ' => 'o','Ở' => 'O','ở' => 'o',
                'Ủ' => 'U','ủ' => 'u','Ử' => 'U','ử' => 'u','Ỷ' => 'Y','ỷ' => 'y',
                // Tilde.
                'Ẫ' => 'A','ẫ' => 'a','Ẵ' => 'A','ẵ' => 'a','Ẽ' => 'E','ẽ' => 'e','Ễ' => 'E','ễ' => 'e','Ỗ' => 'O',
                'ỗ' => 'o','Ỡ' => 'O','ỡ' => 'o','Ữ' => 'U','ữ' => 'u','Ỹ' => 'Y','ỹ' => 'y',
                // Acute accent.
                'Ấ' => 'A','ấ' => 'a','Ắ' => 'A','ắ' => 'a','Ế' => 'E','ế' => 'e','Ố' => 'O','ố' => 'o','Ớ' => 'O',
                'ớ' => 'o','Ứ' => 'U','ứ' => 'u',
                // Dot below.
                'Ạ' => 'A','ạ' => 'a','Ậ' => 'A','ậ' => 'a','Ặ' => 'A','ặ' => 'a','Ẹ' => 'E','ẹ' => 'e','Ệ' => 'E',
                'ệ' => 'e','Ị' => 'I','ị' => 'i','Ọ' => 'O','ọ' => 'o','Ộ' => 'O','ộ' => 'o','Ợ' => 'O','ợ' => 'o',
                'Ụ' => 'U','ụ' => 'u','Ự' => 'U','ự' => 'u','Ỵ' => 'Y','ỵ' => 'y',
                // Vowels with diacritic (Chinese, Hanyu Pinyin).
                'ɑ' => 'a',
                // Macron.
                'Ǖ' => 'U', 'ǖ' => 'u',
                // Acute accent.
                'Ǘ' => 'U', 'ǘ' => 'u',
                // Caron.
                'Ǎ' => 'A','ǎ' => 'a','Ǐ' => 'I','ǐ' => 'i','Ǒ' => 'O','ǒ' => 'o','Ǔ' => 'U','ǔ' => 'u','Ǚ' => 'U',
                'ǚ' => 'u',
                // Grave accent.
                'Ǜ' => 'U','ǜ' => 'u',
            );
     
            // Used for locale-specific rules.
            if ( empty( $locale ) ) {
                $locale = app()->currentLocale();
            }
     
            /*
             * German has various locales (de_DE, de_CH, de_AT, ...) with formal and informal variants.
             * There is no 3-letter locale like 'def', so checking for 'de' instead of 'de_' is safe,
             * since 'de' itself would be a valid locale too.
             */
            if ( str_starts_with( $locale, 'de' ) ) {
                $chars['Ä'] = 'Ae';
                $chars['ä'] = 'ae';
                $chars['Ö'] = 'Oe';
                $chars['ö'] = 'oe';
                $chars['Ü'] = 'Ue';
                $chars['ü'] = 'ue';
                $chars['ß'] = 'ss';
            } elseif ( 'da_DK' === $locale ) {
                $chars['Æ'] = 'Ae';
                $chars['æ'] = 'ae';
                $chars['Ø'] = 'Oe';
                $chars['ø'] = 'oe';
                $chars['Å'] = 'Aa';
                $chars['å'] = 'aa';
            } elseif ( 'ca' === $locale ) {
                $chars['l·l'] = 'll';
            } elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
                $chars['Đ'] = 'DJ';
                $chars['đ'] = 'dj';
            }
     
            $string = strtr( $string, $chars );
        } else {
            $chars = array();
            // Assume ISO-8859-1 if not UTF-8.
            $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
                . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
                . "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
                . "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
                . "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
                . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
                . "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
                . "\xec\xed\xee\xef\xf1\xf2\xf3"
                . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
                . "\xfc\xfd\xff";
     
            $chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';
     
            $string              = strtr( $string, $chars['in'], $chars['out'] );
            $double_chars        = array();
            $double_chars['in']  = array( "\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe" );
            $double_chars['out'] = array( 'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th' );
            $string              = str_replace( $double_chars['in'], $double_chars['out'], $string );
        }
     
        return $string;
    }
}
if (! function_exists('seems_utf8')) {
    function seems_utf8( $str ) {
        mbstring_binary_safe_encoding();
        $length = strlen( $str );
        reset_mbstring_encoding();
        for ( $i = 0; $i < $length; $i++ ) {
            $c = ord( $str[ $i ] );
            if ( $c < 0x80 ) {
                $n = 0; // 0bbbbbbb
            } elseif ( ( $c & 0xE0 ) == 0xC0 ) {
                $n = 1; // 110bbbbb
            } elseif ( ( $c & 0xF0 ) == 0xE0 ) {
                $n = 2; // 1110bbbb
            } elseif ( ( $c & 0xF8 ) == 0xF0 ) {
                $n = 3; // 11110bbb
            } elseif ( ( $c & 0xFC ) == 0xF8 ) {
                $n = 4; // 111110bb
            } elseif ( ( $c & 0xFE ) == 0xFC ) {
                $n = 5; // 1111110b
            } else {
                return false; // Does not match any model.
            }
            for ( $j = 0; $j < $n; $j++ ) { // n bytes matching 10bbbbbb follow ?
                if ( ( ++$i == $length ) || ( ( ord( $str[ $i ] ) & 0xC0 ) != 0x80 ) ) {
                    return false;
                }
            }
        }
        return true;
    }
}
if(! function_exists('mbstring_binary_safe_encoding')) {
    function mbstring_binary_safe_encoding( $reset = false ) {
        static $encodings  = array();
        static $overloaded = null;

        if ( is_null( $overloaded ) ) {
            if ( function_exists( 'mb_internal_encoding' )
                && ( (int) ini_get( 'mbstring.func_overload' ) & 2 ) // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.mbstring_func_overloadDeprecated
            ) {
                $overloaded = true;
            } else {
                $overloaded = false;
            }
        }

        if ( false === $overloaded ) {
            return;
        }

        if ( ! $reset ) {
            $encoding = mb_internal_encoding();
            array_push( $encodings, $encoding );
            mb_internal_encoding( 'ISO-8859-1' );
        }

        if ( $reset && $encodings ) {
            $encoding = array_pop( $encodings );
            mb_internal_encoding( $encoding );
        }
    }
}
if(! function_exists('reset_mbstring_encoding')) {
    function reset_mbstring_encoding() {
        mbstring_binary_safe_encoding( true );
    }
}


?>