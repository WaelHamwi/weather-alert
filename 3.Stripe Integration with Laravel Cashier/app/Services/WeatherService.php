<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('WEATHER_API_KEY');
        $this->baseUrl = env('WEATHER_API_URL');
    }

    /**
     * current weather for user-specified locations
     */
    public function getCurrentWeather($location)
    {
        $response = Http::get("{$this->baseUrl}/weather", [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => 'metric' // or 'imperial' depending on your needs
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Fetch hourly forecast for user-specified locations.
     */
    public function getHourlyForecast($location)
    {
        $response = Http::get("{$this->baseUrl}/forecast", [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => 'metric' // or 'imperial' depending on your needs
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Handle the API response and errors.
     */
    protected function handleResponse($response)
    {
        if ($response->successful()) {
            return $response->json();
        }

        if ($response->clientError()) {
            throw new \Exception('Client error: ' . $response->body());
        }

        if ($response->serverError()) {
            throw new \Exception('Server error: ' . $response->body());
        }

        throw new \Exception('Unexpected error: ' . $response->body());
    }
}
