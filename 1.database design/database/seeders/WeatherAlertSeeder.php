<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeatherAlert;
use App\Models\Location;

class WeatherAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $locations = Location::all(); 

        // Define the different alert types and messages
        $alerts = [
            //alert 1
            [
                'alert_type' => 'Earthquake',
                'message' => 'An earthquake warning has been issued for {location}.',
            ],
             //alert 2
            [
                'alert_type' => 'Flood Warning',
                'message' => 'A flood warning has been issued for {location}.',
            ],
             //alert 3
            [
                'alert_type' => 'Severe Thunderstorm',
                'message' => 'A severe thunderstorm warning has been issued for {location}.',
            ],
        ];

        foreach ($locations as $index => $location) {
            $alert = $alerts[$index % count($alerts)];

            WeatherAlert::create([
                'location_id' => $location->id,
                'alert_type' => $alert['alert_type'],
                'message' => str_replace('{location}', $location->name, $alert['message']),
            ]);
        }
    }
}
