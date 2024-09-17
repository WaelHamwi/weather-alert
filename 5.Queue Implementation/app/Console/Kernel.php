<?php

namespace App\Console;

use App\Jobs\CheckWeatherForUsers;
use App\Services\WeatherService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\TrialExpirationReminder;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Inject the WeatherService and schedule the job
        /*   $schedule->call(function () {
            $weatherService = app(WeatherService::class); //inistiate instance of weather service using app
            dispatch(new CheckWeatherForUsers($weatherService)); //job--->service
        })->everyMinute();*/
        $schedule->call(function () {
            try {
                // Dispatch the TrialExpirationReminder job
                dispatch(new TrialExpirationReminder());

                // Log success
                Log::info('TrialExpirationReminder job executed successfully.');
            } catch (\Exception $e) {
                // Log failure
                Log::error('TrialExpirationReminder job failed: ' . $e->getMessage());
            }
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
