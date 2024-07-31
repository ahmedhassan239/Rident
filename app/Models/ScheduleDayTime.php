<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDayTime extends Model
{
    use HasFactory;
    
    protected $table = 'schedule_day_times';
    protected $fillable = ['schedule_day_id', 'start_from', 'end_to'];

    public function scheduleDay()
    {
        return $this->belongsTo(ScheduleDay::class, 'schedule_day_id');
    }

    // One-to-Many relationship with ScheduleDayTimeSlot
    public function scheduleDayTimeSlots()
    {
        return $this->hasMany(ScheduleDayTimeSlot::class, 'schedule_day_time_id');
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'slot_id');
    }

    protected $casts = [
        'start_from' => 'datetime',
        'end_to' => 'datetime',
    ];
}
