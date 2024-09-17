<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Support\Facades\Auth;
use app\Models\User;
use Stripe\Stripe;
use Carbon\Carbon;

class SubscriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function checkTrial(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login'); // Redirect to login if the user is not authenticated
        }
        //get the difference time to check if the user still has trial days
        $trialDays = now()->lessThan($user->trial_ends_at) ? now()->diffInDays($user->trial_ends_at) : 0;
        if ($trialDays > 0) {
            return view('subscription.trial', compact('trialDays'));
        }
        return $this->checkout($request);
    }
    //we redirect users to checkout if they still have trial period and they want to proceed with the paid plan


    public function checkout(Request $request)
    {
        try {
            $userId = Auth::id();

            // Retrieve the user instance from the User model
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('login');
            }

            Stripe::setApiKey(config('cashier.secret'));

            $plan = $request->input('plan');
            $priceId = $plan === 'yearly' ? config('cashier.yearly_price_id') : config('cashier.monthly_price_id');
            $quantity = 1;

            // Create a Stripe customer if not already created
            if (empty($user->stripe_id)) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Create a Stripe Checkout session
            $checkout_session = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => $quantity
                ]],
                'mode' => 'subscription',
                'customer' => $user->stripe_id, // to distinguish the user from the webhook Associate the customer with the checkout session
                'success_url' => route('subscribe.success', ['plan' => $plan]),
                'cancel_url' => route('subscribe.cancel'),
            ]);
            return redirect($checkout_session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            return back()->withErrors(['error' => 'There was an issue processing your payment: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            // Handle general errors
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }



    public function success(Request $request)
    {
        $plan = $request->query('plan', 'monthly'); // Default to 'monthly'

        return view('subscription.success', ['plan' => $plan]);
    }

    public function renewSubscription(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = User::find($userId);

            if (!$user) {
                return redirect()->route('login');
            }

            Stripe::setApiKey(config('cashier.secret'));
            $plan = $request->input('plan', 'monthly');
            $priceId = $plan === 'yearly' ? config('cashier.yearly_price_id') : config('cashier.monthly_price_id');

            // Create a Stripe customer if not already created
            if (empty($user->stripe_id)) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Create a Stripe Checkout session
            $checkout_session = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'customer' => $user->stripe_id,
                'success_url' => route('subscribe.success', ['plan' => $plan]),
                'cancel_url' => route('subscribe.cancel'),
            ]);

            // Calculate the new subscription end date
            $currentEndDate = $user->subscription_ends_at ? Carbon::parse($user->subscription_ends_at) : null;
            if ($currentEndDate && now()->lessThan($currentEndDate)) {
                $newEndDate = ($plan === 'yearly') ? $currentEndDate->addYear() : $currentEndDate->addMonth();
            } else {
                $newEndDate = ($plan === 'yearly') ? now()->addYear() : now()->addMonth();
            }

            // Update the subscription plan and end date
            $user->subscription_plan = $plan;
            $user->subscription_ends_at = $newEndDate;
            $user->save();

            return redirect($checkout_session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            return back()->withErrors(['error' => 'There was an issue processing your payment: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            // Handle general errors
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }




    public function cancel()
    {
        return view('subscription.cancel');
    }

    public function cancelSubscription()
    {
        $user = Auth::user();
        $userId = Auth::id();
        $user = User::where('id', $userId)->first();
        if ($user && $user->subscription_plan) {
            $user->subscription_ends_at = now();
            $user->subscription_plan = null;
            $user->save();
            return view('subscription.unsubscribed_success');
        }
        return view('subscription.not-subscribed');
    }

    public function customerPortal()
    {
        $stripe = new StripeClient(config('cashier.secret'));

        $user = auth()->user();

        $portalSession = $stripe->billingPortal->sessions->create([
            'customer' => $user->stripe_id,
            'return_url' => route('subscribe.success'),
        ]);

        return redirect($portalSession->url);
    }
}
