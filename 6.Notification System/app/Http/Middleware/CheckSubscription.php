<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        $now = now();
        $subscriptionActive = $user->subscription_ends_at && $user->subscription_ends_at->greaterThan($now);
        $trialActive = $user->trial_ends_at && $user->trial_ends_at->greaterThan($now);
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->subscription_ends_at && now()->greaterThan($user->subscription_ends_at)) {
            return redirect()->route('subscribe.not-subscribed');
        }
        if ($subscriptionActive ||  $trialActive) {
            return $next($request);
        }


        // Redirect to a page or show an error if the user is not subscribed
        return redirect()->route('home')->with('error', 'You need a subscription to access this feature.');
    }
}
