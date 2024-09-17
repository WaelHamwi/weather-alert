<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\User;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all(); // Get all users
        $cities = [
            'London',
            'New York',
            'Paris',
            'Tokyo',
            'Sydney'
        ];

        foreach ($users as $index => $user) {
            // Ensure there are enough cities for users
            $city = $cities[$index % count($cities)];

            Location::create([
                'user_id' => $user->id,
                'name' => $city,
                'latitude' => $this->generateStaticLatitude($city),
                'longitude' => $this->generateStaticLongitude($city),
            ]);
        }
    }

    // Method to return a static latitude based on the city
    private function generateStaticLatitude($city)
    {
        $latitudes = [
            'London' => 51.5074,
            'New York' => 40.7128,
            'Paris' => 48.8566,
            'Tokyo' => 35.6895,
            'Sydney' => -33.8688,
        ];

        return $latitudes[$city] ?? 0;
    }

    // Method to return a static longitude based on the city
    private function generateStaticLongitude($city)
    {
        $longitudes = [
            'London' => -0.1278,
            'New York' => -74.0060,
            'Paris' => 2.3522,
            'Tokyo' => 139.6917,
            'Sydney' => 151.2093,
        ];

        return $longitudes[$city] ?? 0;
    }
}


