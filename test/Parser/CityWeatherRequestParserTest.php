<?php

namespace Parser;

use App\Parser\CityWeatherRequestParser;
use PHPUnit\Framework\TestCase;

class CityWeatherRequestParserTest extends TestCase
{
    /**
     * @dataProvider validRequestDataProvider
     */
    public function testCanSuccessfullyParseValidRequest(string $request, string $expectedCity)
    {
        $parser = new CityWeatherRequestParser();
        $city = $parser->getCityFromRequest($request);

        $this->assertSame($expectedCity, $city);
    }

    public function validRequestDataProvider(): array
    {
        return [
            ['What is the weather like in Brisbane today?', 'Brisbane'],
            ['what is the weather like in brisbane today?', 'brisbane'],
            ['what is the weather like in Mildura–Wentworth today?', 'Mildura–Wentworth'],
            ['what is the weather like in San Francisco today?', 'San Francisco'],
            ['what is the weather like in Bailey\'s Prairie today?', 'Bailey\'s Prairie'],
            ['What is the weather like in Melbourne today?', 'Melbourne'],
            ['What is the weather like in Penzance today?', 'Penzance'],
            ['What is the weather like in Cornwall today?', 'Cornwall'],
        ];
    }
}
