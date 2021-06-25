<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use App\Parser\CityWeatherRequestParser;
use App\Response\WeatherResponse;
use App\Service\WeatherService;
use Laminas\Diactoros\Response\XmlResponse;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;

use function get_class;

class HomePageHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testReturnsTextResponseWhenProperlyFormattedRequestReceived()
    {
        $weatherResponseData = <<<EOF
{
    "location": {
        "name": "Melbourne",
        "region": "Victoria",
        "country": "Australia",
        "lat": -37.82,
        "lon": 144.97,
        "tz_id": "Australia/Melbourne",
        "localtime_epoch": 1624534813,
        "localtime": "2021-06-24 21:40"
    },
    "current": {
        "last_updated_epoch": 1624533300,
        "last_updated": "2021-06-24 21:15",
        "temp_c": 11.0,
        "temp_f": 51.8,
        "is_day": 0,
        "condition": {
            "text": "Partly cloudy",
            "icon": "//cdn.weatherapi.com/weather/64x64/night/116.png",
            "code": 1003
        },
        "wind_mph": 21.7,
        "wind_kph": 34.9,
        "wind_degree": 30,
        "wind_dir": "NNE",
        "pressure_mb": 1005.0,
        "pressure_in": 30.2,
        "precip_mm": 0.0,
        "precip_in": 0.0,
        "humidity": 87,
        "cloud": 75,
        "feelslike_c": 8.5,
        "feelslike_f": 47.4,
        "vis_km": 10.0,
        "vis_miles": 6.0,
        "uv": 1.0,
        "gust_mph": 20.1,
        "gust_kph": 32.4
    }
}
EOF;

        /** @var WeatherService|ObjectProphecy $weatherService */
        $weatherService = $this->prophesize(WeatherService::class);
        $weatherService
            ->getWeatherData('Melbourne')
            ->willReturn(json_decode($weatherResponseData))
            ->shouldBeCalled();

        $weatherParser = new CityWeatherRequestParser();
        $message = new WeatherResponse();

        $homePage = new HomePageHandler($weatherService->reveal(), $weatherParser, $message);

        /** @var ServerRequestInterface|ObjectProphecy $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        $request
            ->getParsedBody()
            ->willReturn([
                'Body' => 'What is the weather like in Melbourne today?'
            ])
            ->shouldBeCalledOnce()
        ;

        $response = $homePage->handle($request->reveal());

        self::assertInstanceOf(XmlResponse::class, $response);
    }

    public function testReturnsTextResponseWhenNoMatchingLocationFoundByWeatherApi()
    {
        $weatherResponseData = <<<EOF
{
    "error": {
        "code": 1006,
        "message": "No matching location found."
    }
}
EOF;

        /** @var WeatherService|ObjectProphecy $weatherService */
        $weatherService = $this->prophesize(WeatherService::class);
        $weatherService
            ->getWeatherData('J')
            ->willReturn(json_decode($weatherResponseData))
            ->shouldBeCalled();

        $weatherParser = new CityWeatherRequestParser();
        $message = new WeatherResponse();

        $homePage = new HomePageHandler($weatherService->reveal(), $weatherParser, $message);

        /** @var ServerRequestInterface|ObjectProphecy $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        $request
            ->getParsedBody()
            ->willReturn([
                'Body' => 'What is the weather like in J today?'
            ])
            ->shouldBeCalledOnce()
        ;

        $response = $homePage->handle($request->reveal());

        self::assertInstanceOf(XmlResponse::class, $response);
    }

    public function testReturnsTextResponseWhenImproperlyFormattedRequestReceived()
    {
        /** @var WeatherService|ObjectProphecy $weatherService */
        $weatherService = $this->prophesize(WeatherService::class);
        $weatherParser = new CityWeatherRequestParser();
        $message = new WeatherResponse();

        $homePage = new HomePageHandler($weatherService->reveal(), $weatherParser, $message);

        /** @var ServerRequestInterface|ObjectProphecy $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        $request
            ->getParsedBody()
            ->willReturn([
                'Body' => 'How is the weather in Melbourne today?'
            ])
            ->shouldBeCalledOnce()
        ;

        $response = $homePage->handle($request->reveal());

        self::assertInstanceOf(XmlResponse::class, $response);
    }
}
