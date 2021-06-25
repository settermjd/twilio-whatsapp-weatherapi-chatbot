<?php

declare(strict_types=1);

namespace App\Response;

use Twilio\TwiML\MessagingResponse;

/**
 * Class WeatherResponse
 * @package App\Response
 */
class WeatherResponse
{
    public function getSuccessResponse(object $weatherData): MessagingResponse
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

    public function getErrorResponse(string $message): MessagingResponse
    {
        $response = new MessagingResponse();
        $response->message($message);

        return $response;
    }
}