<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Doctor extends Model
{
    use HasFactory,SoftDeletes,HasTranslations;
    protected $fillable = [
        'name', 'slug', 
         'sub_specialtie',
        'description', 'overview', 'seo_title', 'seo_keywords', 
        'seo_description', 'robots', 'status', 'featured'
    ];
    protected $dates = ['deleted_at'];
    public $translatable = [
        'name', 'slug', 'description', 'overview',
        'seo_title', 'seo_keywords', 'seo_description',
    ];

    // public function country()
    // {
    //     return $this->belongsTo(Country::class,'country_id');
    // }
    // public function governorate()
    // {
    //     return $this->belongsTo(Governorate::class,'governorate_id');
    // }
    // public function area()
    // {
    //     return $this->belongsTo(Area::class,'area_id');
    // }

    // public function specialty()
    // {
    //     return $this->belongsTo(Specialty::class,'specialtie_id');
    // }

    // public function healthcareProviders()
    // {
    //     return $this->belongsToMany(HealthCareProvider::class, 'healthcare_provider_doctor');
    // }

    public function subSpecialties()
    {
        return $this->hasMany(SubSpecialty::class);
    }

    // public function insurance()
    // {
    //     return $this->hasMany(Insurance::class);
    // }
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
}
