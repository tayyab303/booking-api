<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannedOff extends Model
{
    use HasFactory;
    protected $table = 'planned_offs';
    protected $guarded = [];

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
