<?php
namespace Jm\WpHelper\Helper;

/**
 */
class Php
{
    /**
     * @param $number
     * @return string
     */
    public static function format_number_text($number) : string {
        if(is_numeric($number)) {
            switch(true) {
                case $number >= (float)1E3 && $number < (float)1E6:
                    return round($number / 1E3, 2) . ' tys.';
                    break;
                case $number >= (float)1E6 && $number < (float)1E9:
                    return round($number / 1E6, 2) . ' mln';
                    break;
                case $number >= (float)1E9 && $number < (float)1E12:
                    return round($number / 1E9, 2) . ' mld';
                    break;

                default:
                    return $number;
                    break;
            }
        }
        else {
            return $number;
        }
    }

    /**
     * @param $text
     * @return string
     */
    static public function slugify($text) : string {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * @param $url
     * @return bool
     */
    static public function is_external($url) : bool {
        $host = $_SERVER['SERVER_NAME'];
        $components = parse_url($url);

        if (empty($components['host'])) return false;
        if (strcasecmp($components['host'], $host) === 0) return false;
        return strrpos(strtolower($components['host']), '.' . $host) !== strlen($components['host']) - strlen('.' . $host);
    }

    /**
     * @return bool
     */
    static public function is_ie() : bool {
        return
            strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
                ? true
                : false;
    }


    /**
     * @param $keys
     * @param $array
     * @return bool
     */
    function array_multiple_keys_exists($keys = [], $array = []) : bool {
        if (is_array($keys) && is_array($array) && 0 === count(array_diff($keys, array_keys($array)))) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function starts_with($haystack, $needle) : bool
    {
        $needle = is_array($needle) ? $needle : [$needle];
        foreach($needle as $n) {
            $length = strlen($n);
            if (substr($haystack, 0, $length) === $n) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function ends_with($haystack, $needle) : bool
    {
        $needle = is_array($needle) ? $needle : [$needle];
        foreach($needle as $n) {
            $length = strlen($n);
            if (substr($haystack, -$length) === $n) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $value
     * @return string
     */
    public static function remove_orphan($value) : string {
        $orphans = array('i', 'a', 'z', 'o', 'w', 'co', 'to', 'to,', 'na', 'do', 'za', 'po', 'ale', 'jak', 'lub', 'pod', 'nad', 'dla', 'choć', 'oraz', 'przez', 'przed');

        $find1 = array_map(function($val) { return ' ' . $val . ' '; }, $orphans);
        $find2 = array_map(function($val) { return '&nbsp;' . $val . ' '; }, $orphans);
        $find = array_merge($find1, $find2);

        $replace1 = array_map(function($val) { return ' ' . $val . '&nbsp;'; }, $orphans);
        $replace2 = array_map(function($val) { return '&nbsp;' . $val . '&nbsp;'; }, $orphans);
        $replace = array_merge($replace1, $replace2);

        return str_replace($find, $replace, $value);
    }

    /**
     * @param $length
     * @return string
     */
    public static function generate_random_string($length = 10) : string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $array1
     * @param $array2
     * @return array|null
     */
    public static function sort_array_by_array($array1, $array2) : ?array {
        if(!is_array($array1) || empty($array1) || !is_array($array2) || empty($array2)) {
            return null;
        }

        return array_replace(array_flip($array2), $array1);
    }

    /**
     * @param $array1
     * @param $array2
     * @return array|null
     */
    public static function sort_associative_array_by_array($array1, $array2) : ?array {
        uksort($array1, function($a, $b) use ($array2) {
            $posA = array_search($a, $array2);
            $posB = array_search($b, $array2);
            return $posA - $posB;
        });
        return $array1;
    }

    /**
     * @param $array
     * @param $sum
     * @param $count
     * @return float|int
     */
    public static function avg_multi_array($array, &$sum = 0, &$count = 0) {
        foreach ($array as $element) {
            if (is_array($element)) {
                self::avg_multi_array($element, $sum, $count);
            } else {
                $sum += $element;
                $count++;
            }
        }

        // Return the average at the end of the recursion
        return $count === 0 ? 0 : $sum / $count;
    }
}


