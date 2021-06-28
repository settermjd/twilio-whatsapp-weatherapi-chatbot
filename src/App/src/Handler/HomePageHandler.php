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
use Twilio\TwiML\MessagingResponse;

use function json_decode;
use function stream_get_contents;

class HomePageHandler implements RequestHandlerInterface
{
    public const BASE_URI = 'https://api.weatherapi.com/v1/current.json';
    public const BODY_PATTERN = "/What is the weather like in (?P<city>[a-z\-' ].*) today\?/i";

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody()['Body'];
        preg_match(self::BODY_PATTERN, $body, $matches);

        if (!$matches['city']) {
            return new XmlResponse(
                (string)(new MessagingResponse())
                    ->message('Sorry, but I could not determine the city you asked for.')
            );
        }

        $weatherData = $this->getWeatherData($matches['city']);
        if (property_exists($weatherData, 'error')) {
            return new XmlResponse(
                (string)(new MessagingResponse())
                    ->message($weatherData->error->message)
            );
        }

        return new XmlResponse((string)$this->getSuccessResponse($weatherData));
    }

    private function getWeatherData(string $city): object
    {
        $apiKey = $_ENV['WEATHERAPI_API_KEY'];
        $queryString = http_build_query([
            'key' => $apiKey,
            'q' => $city,
            'aqi' => 'no'
        ]);
        $requestUri = sprintf('%s?%s', self::BASE_URI, $queryString);

        $fh = fopen($requestUri, 'rb');
        $weatherData = json_decode(stream_get_contents($fh));
        fclose($fh);

        return $weatherData;
    }

    private function getSuccessResponse(object $weatherData): MessagingResponse
    {
        $responseString = <<<EOF
In %s (%s, %s), today, it's %d degrees celsius, but feels like %d, with a humidity of %d percent.
The wind is currently %d kp/h from the %s.
EOF;
        $response = new MessagingResponse();
        $response->message(
            sprintf(
                $responseString,
                $weatherData->location->name,
                $weatherData->location->region,
                $weatherData->location->country,
                $weatherData->current->temp_c,
                $weatherData->current->feelslike_c,
                $weatherData->current->humidity,
                $weatherData->current->wind_kph,
                $weatherData->current->wind_dir
            )
        );
        return $response;
    }
}
