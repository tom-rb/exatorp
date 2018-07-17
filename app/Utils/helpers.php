<?php

use Illuminate\Support\Arr;

/**
 * Get a subset of the items from the given array. If a key does not exist,
 * it's value will be null (instead of not including the key in the
 * result, as array_only does).
 */
function array_pick($array, $keys)
{
    $keys = is_array($keys) ? $keys : func_get_args();

    $results = [];

    foreach ($keys as $key) {
        Arr::set($results, $key, data_get($array, $key));
    }

    return $results;
}

/**
 * Get a subset of items from the array. If a key does not exists or
 * its value is null, its not included in the result.
 */
function array_only_clean($array, $keys)
{
    $keys = is_array($keys) ? $keys : func_get_args();

    $results = [];

    foreach ($keys as $key) {
        $value = data_get($array, $key);
        if (!is_null($value))
            Arr::set($results, $key, $value);
    }

    return $results;
}

/**
 * Get the array keys except the specified ones.
 */
function array_keys_except($array, $except)
{
    $except = is_array($except) ? $except : [$except];

    foreach ($except as $key)
        unset($array[$key]);

    return array_keys($array);
}

/**
 * Return array_keys($array) if the array is associative.
 * Otherwise, return the array itself.
 *
 * @note This implementation is not fast, be careful.
 *
 * @param $array
 * @return mixed
 */
function array_keys_if_associative($array)
{
    $isIndexed = array_values($array) === $array;

    return $isIndexed ? $array : array_keys($array);
}

/**
 * Format strings to title case, except some keywords that will
 * always be lowercase as happens in portuguese names.
 *
 * @param $name
 * @return string
 */
function format_pt_name($name)
{
    $ignore = ['do', 'dos', 'da', 'das', 'de', 'e'];

    // array_filter will throw out any falsy value, like '0', but I don't
    // expect a zero in a name anyway.
    $words = array_filter(explode(' ', mb_strtolower($name)));

    foreach ($words as &$word) {
        if (!in_array($word, $ignore))
            $word = mb_convert_case($word, MB_CASE_TITLE);
    }

    return implode(' ', $words);
}

/**
 * Trims and format strings to lower case.
 *
 * @param $email
 * @return string
 */
function format_email($email)
{
    return trim(mb_strtolower($email));
}

/**
 * Slugfy the string also removing accents and non-english chars.
 *
 * @param $title string
 * @param $separator string
 * @return string
 */
function str_slugfy($title, $separator = '-')
{
    return str_slug(no_accents($title), $separator);
}

/**
 * Remove accents and replace non-latin chars with equivalent ones.
 *
 * @param $string
 * @return string
 */
function no_accents($string)
{
    static $map = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
        'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Ŕ' => 'R',
        'Þ' => 's', 'ß' => 'B', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
        'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
        'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y',
        'þ' => 'b', 'ÿ' => 'y', 'ŕ' => 'r'
    ];
    return strtr($string, $map);
}

/**
 * Returns the name of a day of week numeric representation where Monday == 1.
 * I.e., dayOfWeekName(3) == 'Wednesday'
 *
 * @param $day_of_week
 * @return string
 */
function dayOfWeekName($day_of_week)
{
    return ucfirst(\Carbon\Carbon::parse('Sunday')->addDay($day_of_week)->formatLocalized('%A'));
}

/**
 * Get the current url with query (GET) strings.
 *
 * @return string
 */
function url_with_query()
{
    $query = request()->getQueryString();

    $question = request()->getBaseUrl().request()->getPathInfo() == '/' ? '/?' : '?';

    return $query ? request()->decodedPath().$question.$query : request()->decodedPath();
}

/**
 * Return if the current url, including query, has the given pattern.
 *
 * @param string $pattern - Can use wildcard 'example/*' or 'example?q=*'
 * @return bool
 */
function url_is($pattern)
{
    // Add prefix to uri, as it's done to all routes by the app.url_prefix config
    $prefix = config('app.url_prefix');
    $pattern = trim(trim($prefix, '/').'/'.trim($pattern, '/'), '/') ?: '/';

    return str_is($pattern, url_with_query());
}

/**
 * Return whether $value is present in the query string. If a $key is given,
 * the search is restricted to it.
 *
 * @param $value
 * @param null $key
 * @return bool
 */
function query_has($value, $key = null)
{
    if(is_null($key))
        return in_array($value, request()->query());
    else
        return request()->query($key) == $value;
}

/**
 * Returns whether the query string is empty. By default, 'page' attribute
 * is ignored.
 *
 * @param array $ignore
 * @return bool
 */
function query_is_empty($ignore = ['page'])
{
    return empty(array_except(request()->query(), $ignore));
}

/**
 * Wraps a view model in an array.
 *
 * @param $model
 * @return array
 */
function vm($model)
{
    return ['vm' => $model];
}
