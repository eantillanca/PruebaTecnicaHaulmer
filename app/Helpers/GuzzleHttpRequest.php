<?php

namespace App\Helpers;

use Exception;

class GuzzleHttpRequest
{
    static function GuzzleHttpRequest($url, $method, $params)
    {
        try {
            $client             = new \GuzzleHttp\Client();
            $clientResponse     = $client->request($method, $url, ['json' => $params]);
            $response = json_decode($clientResponse->getBody()->getContents());
        } catch (Exception $e) {
            $response = $e->getMessage();
        }
        return $response;
    }
}
