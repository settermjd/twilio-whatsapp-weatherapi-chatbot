<?php

declare(strict_types=1);

namespace App\Handler;

use App\Parser\CityWeatherRequestParser;
use App\Response\WeatherResponse;
use App\Service\WeatherService;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class HomePageHandlerFactory
 * @package App\Handler
 */
class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        // Need to handle the service not being able to be created
        $weatherService = $container->get(WeatherService::class);

        return new HomePageHandler($weatherService, new CityWeatherRequestParser(), new WeatherResponse());
    }
}
