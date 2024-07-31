<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutUs extends Model
{
    use HasFactory,HasTranslations;    
    protected $table = 'aboutus';
    protected $fillable = [
        'name', 'slug', 'mission',
         'vision', 'wy_choose_us', 'about_us','title1','title2','des1','des2',
          'num1', 'num2', 'num3', 'num4','robots',
           'seo_title', 'seo_keywords', 'seo_description'
    ];
    public $translatable = [
        'name', 'slug', 'mission',
        'vision', 'wy_choose_us','title1','title2','des1','des2', 'about_us',
        'seo_title', 'seo_keywords', 'seo_description'
    ]; 
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
}
