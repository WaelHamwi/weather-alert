<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;// import the Auth current user
use App\Models\Location; // import the location model
class WeatherController extends Controller
{
    protected $weatherService;

//inection dependency to get an instance of the service and use its methods by using oop php princple 
    public function __construct(WeatherService $weatherService)  
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Show current weather for user-specified locations.
     */
    public function showCurrentWeather(Request $request)
    {
  
        $user = Auth::user();
        $weather = null;
        $error = null;

        if ($user) {
            // Retrieve the user's location from the database
            $location = Location::where('user_id', $user->id)->first();
          //  dd($location);

            if ($location) {
                try {
                    $weather = $this->weatherService->getCurrentWeather($location->name);
                } catch (\Exception $e) {
                    $error = 'Error fetching current weather: ' . $e->getMessage();
                    Log::error($error);
                }
            } else {
                $error = 'Location not found for the user.';
            }
        } else {
            $error = 'User not authenticated.';
        }

        return view('current-weather', compact('weather', 'error', 'location'));
    }

    /**
     * Show hourly forecast for user-specified locations.
     */
    public function showHourlyForecast(Request $request)
    {
        $user = Auth::user();
        $forecast = null;
        $error = null;

        if ($user) {
            // Retrieve the user's location from the database
            $location = Location::where('user_id', $user->id)->first();

            if ($location) {
                try {
                    $forecast = $this->weatherService->getHourlyForecast($location->name);
                } catch (\Exception $e) {
                    $error = 'Error fetching hourly forecast: ' . $e->getMessage();
                    Log::error($error);
                }
            } else {
                $error = 'Location not found for the user.';
            }
        } else {
            $error = 'User not authenticated.';
        }

        return view('hourly-forecast', compact('forecast', 'error'));
    }
}
