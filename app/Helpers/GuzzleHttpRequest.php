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

    static function GetMockapiData()
    {
        try {
            $client             = new \GuzzleHttp\Client();
            $clientResponse     = $client->request("GET", env("MOCKAPI_URL"), ['json' => []]);
            $response = json_decode($clientResponse->getBody()->getContents())[0];
            $response = collect($response);
        } catch (Exception $e) {
            $response = $e->getMessage();
        }
        return $response;
    }
}
