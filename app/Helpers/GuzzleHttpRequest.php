<?php

namespace App\Helpers;


class GuzzleHttpRequest
{
    static function GuzzleHttpRequest($url, $method, $params)
    {
        $client             = new \GuzzleHttp\Client();
        $clientResponse     = $client->request($method, $url, ['json' => $params]);
        $response = json_decode($clientResponse->getBody()->getContents());
        return $response;
    }
}
