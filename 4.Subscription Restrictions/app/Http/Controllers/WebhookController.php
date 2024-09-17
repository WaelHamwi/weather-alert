<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use App\Models\User;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        /*   [2024-09-10 16:44:58] local.INFO: Handling checkout session completed: 
        {"id":"cs_test_a1UnAuUdqMKAJ9jrZUWPH30VWjQwp1bGRMpWhg6fvJNIqyw1yiZby3ANBs",
            "object":"checkout.session","after_expiration":null,
            "allow_promotion_codes":null,"amount_subtotal":1300,
            "amount_total":1300,"automatic_tax":{"enabled":false,
                "liability":null,"status":null},"billing_address_collection":null,
                "cancel_url":"http:\/\/localhost:8000\/cancel","client_reference_id":null,
                "client_secret":null,"consent":null,"consent_collection":null,"created":1725986676,
                "currency":"usd","currency_conversion":null,"custom_fields":[],
                "custom_text":{"after_submit":null,"shipping_address":null,"submit":null,
                    "terms_of_service_acceptance":null},"customer":"cus_QpBYmyKFP0Ss9K","customer_creation":"always",
                    "customer_details":{"address":{"city":null,"country":"EG","line1":null,"line2":null,
                        "postal_code":null,"state":null},"email":"waellhamwii@gmail.com","name":"324","phone":null,
                        "tax_exempt":"none","tax_ids":[]},"customer_email":null,"expires_at":1726073076,
                        "invoice":"in_1PxXBTDdUcew3hl2ELj777os","invoice_creation":null,"livemode":false,
                        "locale":null,"metadata":[],"mode":"subscription","payment_intent":null,"payment_link":null,
                        "payment_method_collection":"always","payment_method_configuration_details":null,
                        "payment_method_options":{"card":{"request_three_d_secure":"automatic"}},
                        "payment_method_types":["card"],"payment_status":"paid","phone_number_collection":{"enabled":false},
                        "recovered_from":null,
                        "saved_payment_method_options":{"allow_redisplay_filters":["always"],"payment_method_remove":null,
                            "payment_method_save":null},"setup_intent":null,"shipping_address_collection":null,
                            "shipping_cost":null,"shipping_details":null,"shipping_options":[],"status":"complete",
                            "submit_type":null,"subscription":"sub_1PxXBTDdUcew3hl2fzJ59WvX",
                            "success_url":"http:\/\/localhost:8000\/success?plan=monthly",
                            "total_details":{"amount_discount":0,"amount_shipping":0,"amount_tax":0},
                            "ui_mode":"hosted","url":null}  */

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, config('cashier.webhook.secret'));
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info("Received event: {$event->type}");

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            default:
                Log::info("Unhandled event type: {$event->type}");
        }

        return response()->json(['status' => 'success']);
    }

    protected function updateUserSubscription($user, $subscriptionData)
    {
        Log::info("Updating user subscription: " . json_encode($subscriptionData));

        $trialEnd = isset($subscriptionData['trial_end']) ? Carbon::createFromTimestamp($subscriptionData['trial_end']) : null;
        $subscriptionEnd = isset($subscriptionData['current_period_end']) ? Carbon::createFromTimestamp($subscriptionData['current_period_end']) : null;

        $user->subscription_plan = $subscriptionData['plan'] ?? $user->subscription_plan;
        $user->trial_ends_at = $trialEnd;
        $user->subscription_ends_at = $subscriptionEnd;
        $user->save();
    }
    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info("Handling checkout session completed: " . json_encode($session));

        $user = User::where('stripe_id', $session->customer)->first();

        if ($user) {
            $subscriptionData = [
                'plan' => $session->mode ?? "subs",
                'trial_end' => $session->subscription->trial_end ?? null,
                'current_period_end' => $session->expires_at ?? null,
            ];

            $this->updateUserSubscription($user, $subscriptionData);
        } else {
            Log::error("User not found for Stripe customer ID: {$session->customer}");
        }
    }

    protected function handleSubscriptionUpdated($subscription)
    {
        Log::info("Handling subscription updated: " . json_encode($subscription));

        $user = User::where('stripe_id', $subscription->customer)->first();

        if ($user) {
            $subscriptionData = [
                'plan' => $subscription->items->data[0]->price->id ?? null,
                'trial_end' => $subscription->trial_end ?? null,
                'current_period_end' => $subscription->current_period_end ?? null,
            ];

            $this->updateUserSubscription($user, $subscriptionData);
        } else {
            Log::error("User not found for Stripe customer ID: {$subscription->customer}");
        }
    }



    protected function handleSubscriptionDeleted($subscription)
    {
        Log::info("Handling subscription deleted: " . json_encode($subscription));

        $user = User::where('stripe_id', $subscription->customer)->first();

        if ($user) {
            $user->subscription_plan = null;
            $user->trial_ends_at = null;
            $user->subscription_ends_at = null;
            $user->save();
        } else {
            Log::error("User not found for Stripe customer ID: {$subscription->customer}");
        }
    }
}
