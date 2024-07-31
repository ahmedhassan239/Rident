<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDay extends Model
{
    use HasFactory;
    protected $table = 'schedule_days';
    protected $fillable = ['schedule_id', 'day_number','day_name', 'status'];
    
    // public function scheduleDayTimes()
    // {
    //     return $this->hasMany(ScheduleDayTime::class, 'schedule_day_id');
    // }
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'slot_id');
    }
    public function scheduleDayTimes() {
        return $this->hasMany(ScheduleDayTime::class);
    }

}
