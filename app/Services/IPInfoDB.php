<?php

namespace App\Services;

use App\Services\IPInfoDB\City;

class IPInfoDB
{
    private $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;

        $this->apiKey = config('services.ipinfodb.api_key');
    }

    public static function city($ip)
    {
        return (new static($ip))->findCity();
    }

    protected function findCity()
    {
        $url = 'http://api.ipinfodb.com/v3/ip-city/?' .
            http_build_query([
                'key' => $this->apiKey,
                'ip' => $this->ip,
                'format' => 'json',
            ]);

        $result = file_get_contents($url);

        $result = json_decode($result);

        return $result;
    }
}