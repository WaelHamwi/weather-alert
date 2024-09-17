<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Notifications\TrialExpirationNotification;
use Carbon\Carbon;

class TrialExpirationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $expirationDate = Carbon::now()->addDays(3); // Modify as needed for how soon you want to notify

        $users = User::where('subscription_ends_at', '<=', $expirationDate)
            ->orWhere('trial_ends_at', '<=', $expirationDate)
            ->get();

        foreach ($users as $user) {
            // Ensure that the date is formatted properly
            $formattedDate = $expirationDate->format('F j, Y');

            $user->notify(new TrialExpirationNotification(
                "Your trial is about to expire on $formattedDate. Please renew your subscription.",
                url('/subscribe/renew'),
                $expirationDate
            ));
        }

    }
}
