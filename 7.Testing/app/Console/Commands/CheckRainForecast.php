<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\RainForecastNotification;
use App\Notifications\SevereWeatherAlertNotification;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckRainForecast extends Command
{
    protected $signature = 'weather:check-rain';
    protected $description = 'Check if rain is forecasted in the next hour and notify users';

    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        // Fetch users with their associated locations
        $users = User::with('locations')->get();

        foreach ($users as $user) {
            foreach ($user->locations as $location) {

                // Get forecast for the location by city name or coordinates
                if ($location->name) {
                    $forecast = $this->weatherService->getHourlyForecast($location->name);
                    $alerts = $this->weatherService->getSevereWeatherAlerts(null, null, $location->name); // Pass city name if available
                } elseif ($location->latitude && $location->longitude) {
                    $forecast = $this->weatherService->getHourlyForecast(null, $location->latitude, $location->longitude);
                    $alerts = $this->weatherService->getSevereWeatherAlerts($location->latitude, $location->longitude); // Pass lat and lon if available
                } else {
                    Log::warning("Location data is missing for user ID: {$user->id} and location ID: {$location->id}");
                    continue;
                }

                //  rain is expected at this location
                if ($this->isRainExpected($forecast)) {
                    $this->notifyUser($user);
                }

                //severe weather alerts 
                if ($this->hasSevereWeatherAlert($alerts)) {
                    $this->notifyUserSevereAlert($user, $alerts);
                }
            }
        }
    }

    protected function isRainExpected($forecast)
    {
        foreach ($forecast['list'] as $entry) {
            if (isset($entry['rain'])) {
                return true;
            }
        }
        return false;
    }

    protected function hasSevereWeatherAlert($alerts)
    {
        // Check if there are any severe weather alerts
        return !empty($alerts);
    }

    protected function notifyUser($user)
    {
        $message = 'Rain is forecasted in the next hour for your location.';
        $url = url('/weather-details');

        $user->notify(new RainForecastNotification($message, $url));
    }

    protected function notifyUserSevereAlert($user, $alerts)
    {
        if ($user instanceof User && $user->locations->isNotEmpty()) {
            if (is_array($alerts) && !empty($alerts)) {
               
                $location = $user->locations->first();
                $locationName = $location->name;
    
                $message = "Severe weather alert for your location: $locationName.";
            } else {
                $message = 'No severe weather alerts available for your location.';
            }
    
            $url = url('/weather-alerts');
            $user->notify(new SevereWeatherAlertNotification($message, $url));
        } else {
            Log::warning('Authenticated user has no locations available for severe weather alert.');
        }
    }
    
}
