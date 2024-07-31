<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Spatie\Translatable\HasTranslations;

class HealthcareProvider extends Model
{
    use HasFactory,SoftDeletes,HasTranslations;
    protected $fillable = [
        'name', 'slug', 'type', 'country_id', 'governorate_id', 'city_id',
        'description', 'overview', 'seo_title', 'seo_keywords', 'seo_description', 
        'robots', 'status', 'featured','fees','waiting_time','country_sort',
        'governorate_sort','area_sort','specialty_sort'
    ];
    protected $dates = ['deleted_at'];

    public $translatable = [
        'name', 'slug', 'description', 'overview',
        'seo_title', 'seo_keywords', 'seo_description',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function governorate()
    {
        return $this->belongsTo(Governorate::class,'governorate_id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class,'area_id');
    }
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'healthcare_provider_specialty');
    }

    public function insurances()
    {
        return $this->belongsToMany(Insurance::class, 'healthcare_provider_insurance');
    }
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'healthcare_provider_doctor');
    }
    public function branches()
    {
        return $this->belongsToMany(Branche::class, 'healthcare_provider_branch','healthcare_provider_id','branch_id');
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }

}
