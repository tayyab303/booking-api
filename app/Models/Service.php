<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function timeSlot(){
        return $this->hasOne(TimeSlot::class);
    }

    public function plannedOffs(){
        return $this->hasMany(PlannedOff::class);
    }

    public function bookings(){
        return $this->hasMany(Booking::class);
    }
}
