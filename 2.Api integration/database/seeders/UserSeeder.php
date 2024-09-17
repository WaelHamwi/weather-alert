<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //user 1
        User::create([
            'name' => 'Wael Hamwi',
            'email' => 'waellhamwii@gmail.com',
            'password' => Hash::make('test'),
        ]);
        //user 2
        User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
        ]);
        //user 3
        User::create([
            'name' => 'test1',
            'email' => 'test1@test.com',
            'password' => Hash::make('test'),
        ]);
    }
}
