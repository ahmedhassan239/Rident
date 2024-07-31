<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class SubSpecialty extends Model
{
    use HasFactory, SoftDeletes,HasTranslations;

    protected $fillable = [
        'name', 'slug', 'description', 'specialtie_id', 'overview','country_id', 
        'seo_title', 'seo_keywords', 'seo_description', 'robots', 'status'
    ];

    public $translatable = [
        'name', 'slug', 'description','overview', 
        'seo_title', 'seo_keywords', 'seo_description',
    ];

    protected $dates = ['deleted_at'];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }


    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
}


