<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = ['provider_id', 'schedule_gap', 'schedule_meeting_time'];


    public function scheduleDays()
    {
        return $this->hasMany(ScheduleDay::class, 'schedule_id');
    }
    // public function scheduleDayTimes()
    // {
    //     return $this->hasMany(ScheduleDayTime::class);
    // }
}
