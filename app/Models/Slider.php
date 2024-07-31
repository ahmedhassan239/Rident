<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Slider extends Model
{
    use HasFactory,HasTranslations;
    protected $table = 'sliders';

    // Optional: Define which attributes are mass assignable
    protected $fillable = ['title', 'description', 'link_name', 'link'];
    public $translatable = ['title', 'description', 'link_name', 'link'];

    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
}
