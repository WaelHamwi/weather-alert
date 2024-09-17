<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;

class WeatherServiceTest extends TestCase
{
    /** @test */
    public function it_gets_severe_weather_alerts()
    {
        // Mock the HTTP facade to return a predefined response
        Http::fake([
            'api.weather.com/alerts' => Http::response([
                'error' => 404,
                'message' => '{"cod":"404","message":"Internal Server Error"}'
            ], 404),
        ]);

        $weatherService = new WeatherService();
        $latitude = '37.7749'; 
        $longitude = '-122.4194';

        $alerts = $weatherService->getSevereWeatherAlerts($latitude, $longitude);

       
        $this->assertIsArray($alerts);
        $this->assertArrayHasKey('error', $alerts);
        $this->assertArrayHasKey('message', $alerts);
        $this->assertEquals(404, $alerts['error']);
    }
}
