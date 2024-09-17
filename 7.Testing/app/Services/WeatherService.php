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

    //get the alert in the case of sevre weather
    public function getSevereWeatherAlerts($lat, $lon)
    {
        // Make the HTTP request to the "/alerts" endpoint (if available)
        $response = Http::get("{$this->baseUrl}/alerts", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey
        ]);

        // Handle the response
        if ($response->successful()) {
            return $response->json(); // Return the response as JSON
        } else {
            // Handle errors or unsuccessful responses
            return [
                'error' => $response->status(),
                'message' => $response->body()
            ];
        }
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
