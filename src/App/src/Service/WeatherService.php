<?php

declare(strict_types=1);

namespace App\Service;

use function json_decode;
use function stream_get_contents;

/**
 * Class WeatherService
 * @package App\Service
 */
class WeatherService
{
    public const BASE_URI = 'https://api.weatherapi.com/v1/current.json';

    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getWeatherData(string $city): object
    {
        $queryString = http_build_query([
            'key' => $this->apiKey,
            'q' => $city,
            'aqi' => 'no'
        ]);
        $requestUri = sprintf('%s?%s', self::BASE_URI, $queryString);

        $fh = fopen($requestUri, 'rb');
        $weatherData = json_decode(
            stream_get_contents($fh)
        );
        fclose($fh);

        return $weatherData;
    }
}