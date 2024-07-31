<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'date','branch', 'service_id',
     'slot_id', 'lang','status','age','country','source'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scheduleDayTime()
    {
        return $this->belongsTo(ScheduleDayTime::class, 'slot_id');
    }
    public function scheduleDayTimeSlot()
    {
        return $this->belongsTo(ScheduleDayTimeSlot::class, 'slot_id');
    }
}
