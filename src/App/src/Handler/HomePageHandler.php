<?php

declare(strict_types=1);

namespace App\Handler;

use App\Parser\CityWeatherRequestParser;
use App\Response\WeatherResponse;
use App\Service\WeatherService;
use Laminas\Diactoros\Response\XmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class HomePageHandler
 * @package App\Handler
 */
class HomePageHandler implements RequestHandlerInterface
{
    private CityWeatherRequestParser $requestParser;
    private WeatherService $weatherService;
    private WeatherResponse $message;

    public function __construct(
        WeatherService $weatherService,
        CityWeatherRequestParser $weatherParser,
        WeatherResponse $message
    ) {
        $this->message = $message;
        $this->requestParser = $weatherParser;
        $this->weatherService = $weatherService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody()['Body'];
        $city = $this->requestParser->getCityFromRequest($body);

        if (!$city) {
            return new XmlResponse(
                (string)$this->message->getErrorResponse('Sorry, but I could not determine the city you asked for.')
            );
        }

        $weatherData = $this->weatherService->getWeatherData($city);
        if (property_exists($weatherData, 'error')) {
            return new XmlResponse((string)$this->message->getErrorResponse($weatherData->error->message));
        }

        return new XmlResponse((string)$this->message->getSuccessResponse($weatherData));
    }
}
