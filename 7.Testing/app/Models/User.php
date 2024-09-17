<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Billable;
    use Notifiable;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_id',
        'subscription_plan',
        'trial_started_at',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //relation with location 
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    // using the carbon to parse the date attribute to avoid the type
    public function getSubscriptionEndsAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    /**
     * Get the trial end date as a Carbon instance.
     *
     * @param  string|null  $value
     * @return \Carbon\Carbon|null
     */
    public function getTrialEndsAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
}
