<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;
    protected $table = 'time_slots';
    protected $guarded = [];

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
