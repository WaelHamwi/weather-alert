<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserHasLocations()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
            'stripe_id' => 'test_stripe_id',
            'subscription_plan' => 'basic',
            'trial_started_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'subscription_ends_at' => null,
        ]);

        // Create a location associated with the user
        $location = Location::create([
            'user_id' => $user->id,
            'name' => 'Test Location',
            'latitude' => 12.345678,
            'longitude' => 98.765432,
        ]);

        // Refresh the user instance to include relationships
        $user = $user->fresh('locations');

        // Check if the user's locations contain the created location
        $this->assertTrue($user->locations->contains($location));
    }
}
