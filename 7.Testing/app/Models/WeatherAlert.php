<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherAlert extends Model
{
    use HasFactory;

    //relation with the location
    protected $fillable = ['location_id', 'alert_type', 'message'];
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
