<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDayTimeSlot extends Model
{
    use HasFactory;
    protected $table = 'schedule_day_time_slots';
    protected $fillable = ['schedule_day_time_id', 'start_from', 'end_to'];

    public function scheduleDayTime()
    {
        return $this->belongsTo(ScheduleDayTime::class, 'schedule_day_time_id');
    }
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'slot_id');
    }
}
