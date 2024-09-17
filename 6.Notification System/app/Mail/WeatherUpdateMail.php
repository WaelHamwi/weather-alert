<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeatherUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $formattedWeatherData;

    public function __construct($user, $weatherData)
    {
        $this->user = $user;

        // If $weatherData is a JSON string, decode it to an array
        $data = is_array($weatherData) ? $weatherData : json_decode($weatherData, true);

        // Format the weather data
        $this->formattedWeatherData = $this->formatWeatherData($data);
    }

    private function formatWeatherData(array $data)
    {
        $location = $data['name'] . ', ' . $data['sys']['country'];
        $coordinates = 'Latitude ' . $data['coord']['lat'] . ', Longitude ' . $data['coord']['lon'];
        $weatherCondition = $data['weather'][0]['description'];
        $temperature = $data['main']['temp'] . '°C (Feels like ' . $data['main']['feels_like'] . '°C)';
        $pressure = $data['main']['pressure'] . ' hPa';
        $humidity = $data['main']['humidity'] . '%';
        $visibility = $data['visibility'] . ' meters';
        $windSpeed = $data['wind']['speed'] ?? 'unknown';
        $windDirection = $data['wind']['deg'] ?? 'unknown';
        $windGust = isset($data['wind']['gust']) ? $data['wind']['gust'] . ' m/s' : 'no gust data';

        $wind = 'Speed ' . $windSpeed . ' m/s, Direction ' . $windDirection . '°, Gusts ' . $windGust;
        $cloudCover = $data['clouds']['all'] . '%';
        $sunrise = date('H:i:s', $data['sys']['sunrise']);
        $sunset = date('H:i:s', $data['sys']['sunset']);

        // Format the output
        $output = "Location: $location\n";
        $output .= "Coordinates: $coordinates\n";
        $output .= "Weather: $weatherCondition\n";
        $output .= "Temperature: $temperature\n";
        $output .= "Pressure: $pressure\n";
        $output .= "Humidity: $humidity\n";
        $output .= "Visibility: $visibility\n";
        $output .= "Wind: $wind\n";
        $output .= "Cloud Cover: $cloudCover\n";
        $output .= "Sunrise: $sunrise UTC\n";
        $output .= "Sunset: $sunset UTC\n";

        return $output;
    }

    public function build()
    {
        return $this->view('emails.weather_update')
            ->with([
                'userName' => $this->user->name,
                'weatherData' => $this->formattedWeatherData,
            ]);
    }
}
