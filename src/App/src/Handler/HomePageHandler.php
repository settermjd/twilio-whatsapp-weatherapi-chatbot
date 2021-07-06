<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\XmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twilio\TwiML\MessagingResponse;

use function json_decode;
use function stream_get_contents;

class HomePageHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody()['Body'];
        $response = new MessagingResponse();

        $weatherData = $this->getWeatherData($body);
        if (property_exists($weatherData, 'error')) {
            return new XmlResponse(
                (string)$response->message($weatherData->error->message)
            );
        }

        $responseString = <<<EOF
In %s (%s, %s), today, it's %d degrees celsius, but feels like %d, with a humidity of %d percent. The wind is currently %d km/h from the %s.
EOF;
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

        return new XmlResponse((string)$response);
    }

    private function getWeatherData(string $city): object
    {
        $queryString = http_build_query([
            'key' => $_ENV['WEATHERAPI_API_KEY'],
            'q' => $city,
            'aqi' => 'no'
        ]);
        $requestUri = sprintf(
            '%s?%s',
            'https://api.weatherapi.com/v1/current.json',
            $queryString
        );

        $fh = fopen($requestUri, 'rb');
        $weatherData = json_decode(stream_get_contents($fh));
        fclose($fh);

        return $weatherData;
    }

}
