<?php

namespace App;

class Base64
{
    /**
     * Decode fixed base64 data;
     *
     * @param $encodedData
     * @return mixed
     */
    public static function decode($encodedData)
    {
        return base64_decode(
            static::fix($encodedData)
        );
    }

    /**
     * Fix transferred base64 data.
     *
     * @param $data
     * @return mixed
     */
    public static function fix($data)
    {
        return strtr($data, '-_', '+/');
    }
}