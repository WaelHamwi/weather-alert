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

        foreach ($users as $user) {
            Location::create([
                'user_id' => $user->id,
                'name' => $user->name . "'s Home",
                'latitude' => $this->generateRandomLatitude(),
                'longitude' => $this->generateRandomLongitude(),
            ]);
        }
    }

     //private method to be invoked and get randomly latitude
    private function generateRandomLatitude()
    {
        return rand(-90000000, 90000000) / 1000000; 
    }

     //private method to be invoked and get randomly longtiude
    private function generateRandomLongitude()
    {
        return rand(-180000000, 180000000) / 1000000; 
    }
}
