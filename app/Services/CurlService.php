<?php

namespace App\Services;

class CurlService
{
    public static function testUrl(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ((int) $code) === 200;
    }

    public static function getContent(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.example.com/1');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/sgranados.0 (compatible;)");
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
