<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasFactory , HasTranslations;
    protected $fillable = [
        'title',
        'description',
        'service_id',
        'link','status',
    ];
    public $translatable = [
        'title', 'description',
    ];
    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
}
