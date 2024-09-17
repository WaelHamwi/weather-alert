<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeatherUpdateMail;
use Illuminate\Support\Facades\Log;

class CheckWeatherForUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $weatherService;

    /**
     * Create a new job instance.
     *
     * @param WeatherService $weatherService
     * @return void
     */
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $activeUsers = User::whereNotNull('subscription_ends_at')
                ->where('subscription_ends_at', '>', now())
                ->with('locations') // Eager load locations
                ->get();

            if ($activeUsers->isEmpty()) {
                Log::info('No active users found for weather updates.');
                return;
            }

            foreach ($activeUsers as $user) {
                $locations = $user->locations; // This should be a collection of Location models

                if ($locations->isEmpty()) {
                    Log::info("User {$user->id} has no locations.");
                    continue;
                }

                foreach ($locations as $location) {
                    //  $lat = (float) $location->latitude;
                    // $lon = (float) $location->longitude;
                    // Access properties directly if this is a model instance
                    try {
                        $weatherData = $this->weatherService->getCurrentWeather(
                            $location->name
                        );

                        Mail::to($user->email)->send(new WeatherUpdateMail($user, $weatherData));
                    } catch (\Exception $e) {
                        Log::error("Failed to send weather update email to user {$user->id}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to execute CheckWeatherForUsers job: ' . $e->getMessage());
        }
    }
}
