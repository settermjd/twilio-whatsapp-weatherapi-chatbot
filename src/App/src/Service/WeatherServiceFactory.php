<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;

class WeatherServiceFactory
{
    public function __invoke(ContainerInterface $container): WeatherService
    {
        return new WeatherService($_ENV['WEATHERAPI_API_KEY']);
    }
}