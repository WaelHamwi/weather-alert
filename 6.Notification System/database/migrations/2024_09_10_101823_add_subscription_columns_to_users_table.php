<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_id')->nullable()->after('email'); // Stripe customer ID
            $table->string('subscription_plan')->nullable()->after('stripe_id'); // the subscription plan (year or month)
            $table->timestamp('trial_started_at')->nullable(); //save the trial start to be first time the user register the account
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_plan'); // column to save Trial end date
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at'); //column to save  Subscription end date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_id', 'subscription_plan', 'trial_started_at', 'trial_ends_at', 'subscription_ends_at']);
        });
    }
};
