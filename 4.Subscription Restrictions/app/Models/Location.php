<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'latitude', 'longitude'];

    //relation with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     //relation with weather alert
    public function weatherAlerts()
    {
        return $this->hasMany(WeatherAlert::class);
    }
}
