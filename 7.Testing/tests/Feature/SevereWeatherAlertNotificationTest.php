<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\SevereWeatherAlertNotification;

class SevereWeatherAlertNotificationTest extends TestCase
{
    public function testNotificationIsSent()
    {
        $user = User::factory()->create();
        $user->notify(new SevereWeatherAlertNotification('message', 'url'));

        $this->assertDatabaseHas('notifications', [
            'type' => SevereWeatherAlertNotification::class,
            'notifiable_id' => $user->id,
        ]);
    }
}
