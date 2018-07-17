<?php

function create($class, $amountOrOverrides = [], $overrides = [], $states = [])
{
    if (is_integer($amountOrOverrides)) {
        $amount = $amountOrOverrides;
    }
    else {
        $amount = null; // using 1 would make Laravel return a collection of 1 item
        $overrides = $amountOrOverrides;
    }

    if (empty($states))
        return factory($class, $amount)->create($overrides);
    else
        return factory($class, $amount)->states($states)->create($overrides);
}

function createState($class, $states, $amountOrOverrides = [], $overrides = [])
{
    return create($class, $amountOrOverrides, $overrides, $states);
}

function make($class, $amountOrOverrides = [], $overrides = [], $states = [])
{
    if (is_integer($amountOrOverrides)) {
        $amount = $amountOrOverrides;
    }
    else {
        $amount = null;
        $overrides = $amountOrOverrides;
    }

    if (empty($states))
        return factory($class, $amount)->make($overrides);
    else
        return factory($class, $amount)->states($states)->make($overrides);
}

function makeState($class, $states, $amountOrOverrides = [], $overrides = [])
{
    return make($class, $amountOrOverrides, $overrides, $states);
}

function makeRaw($class, $overridesOrState = [], $states = [])
{
    return make($class, 1, $overridesOrState, $states)->first()->attributesToArray();
}

if (!function_exists('quotes'))
{
    /**
     * Surround a string with double quotes.
     *
     * @param $string
     * @return string
     */
    function quotes($string)
    {
        return '"'.$string.'"';
    }
}

if (!function_exists('je'))
{
    /**
     * Json encode
     *
     * @param $data
     * @return string
     */
    function je($data)
    {
        $json = json_encode($data);

        if ($json && $json[0] == '"')
            $json = mb_substr($json, 1, -1); // trim quotes

        return $json;
    }
}