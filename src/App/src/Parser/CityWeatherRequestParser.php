<?php

declare(strict_types=1);

namespace App\Parser;


class CityWeatherRequestParser
{
    public const BODY_PATTERN = "/What is the weather like in (?P<city>[a-z\-' ].*) today\?/i";

    public function getCityFromRequest(?string $body): ?string
    {
        return (preg_match(self::BODY_PATTERN, $body, $matches))
            ? $matches['city']
            : null;
    }
}
