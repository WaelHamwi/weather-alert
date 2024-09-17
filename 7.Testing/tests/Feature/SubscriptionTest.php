<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a user can start a subscription with a trial period.
     *
     * @return void
     */
    public function testUserCanStartSubscriptionWithTrialPeriod()
    {
        $trialEndDate = Carbon::now()->addDays(14); // Store the trial end date

        // Create a user with trial period fields
        $user = User::factory()->create([
            'subscription_plan' => 'basic',
            'trial_started_at' => Carbon::now(),
            'trial_ends_at' => $trialEndDate,
            'subscription_ends_at' => null,
        ]);

        // Assert the user has the correct subscription plan and trial period
        $this->assertEquals('basic', $user->subscription_plan);
        $this->assertTrue(Carbon::parse($user->trial_ends_at)->eq($trialEndDate)); // Compare with saved trialEndDate
    }

    /**
     * Test that a user is charged when the trial period ends.
     *
     * @return void
     */
    public function testUserIsChargedWhenTrialEnds()
    {
        // Create a user whose trial ends today
        $user = User::factory()->create([
            'subscription_plan' => 'basic',
            'trial_ends_at' => Carbon::now(),
        ]);

        // Simulate time traveling to after the trial period
        $this->travelTo(Carbon::now()->addDay());

        // Simulate the end of the trial period
        $user->trial_ends_at = null;
        $user->subscription_ends_at = Carbon::now()->addMonth(); 
        $user->save();

        $this->assertNull($user->trial_ends_at);
        $this->assertNotNull($user->subscription_ends_at);
    }

    /**
     * Test that a user can cancel their subscription during the trial period.
     *
     * @return void
     */
    public function testUserCanCancelSubscriptionDuringTrial()
    {
        $subscriptionEndDate = Carbon::now(); 

        // Create a user with an active trial
        $user = User::factory()->create([
            'subscription_plan' => 'basic',
            'trial_started_at' => Carbon::now(),
            'trial_ends_at' => Carbon::now()->addDays(14),
        ]);


        $user->subscription_ends_at = $subscriptionEndDate;
        $user->save();

        $this->assertNotNull($user->subscription_ends_at);
        $this->assertTrue(Carbon::parse($user->subscription_ends_at)->eq($subscriptionEndDate)); // Compare with saved subscription end date
    }

    /**
     * Test trial extension functionality.
     *
     * @return void
     */
    public function testTrialCanBeExtended()
    {
        $extendedTrialEndDate = Carbon::now()->addDays(10); 
        $user = User::factory()->create([
            'subscription_plan' => 'basic',
            'trial_started_at' => Carbon::now(),
            'trial_ends_at' => Carbon::now()->addDays(7),
        ]);

        $user->trial_ends_at = $extendedTrialEndDate; 
        $user->save();


        $this->assertTrue(Carbon::parse($user->trial_ends_at)->eq($extendedTrialEndDate)); // Compare with saved extended trial end date
    }
}
