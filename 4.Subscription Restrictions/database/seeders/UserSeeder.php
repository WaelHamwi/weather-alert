<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $currentDate = Carbon::now(); // get the current date we should check utc in our laravel project

        //user 1
        User::create([
            'name' => 'Wael Hamwi',
            'email' => 'waellhamwii@gmail.com',
            'password' => Hash::make('test'),
            'trial_started_at' => $currentDate,
            'trial_ends_at' => $currentDate->copy()->addDays(7),
        ]);
        //user 2
        User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
            'trial_started_at' => $currentDate,
            'trial_ends_at' => $currentDate->copy()->addDays(7),
        ]);
        //user 3
        User::create([
            'name' => 'test1',
            'email' => 'test1@test.com',
            'password' => Hash::make('test'),
            'trial_started_at' => $currentDate,
            'trial_ends_at' => $currentDate->copy()->addDays(7),
        ]);
    }
}
