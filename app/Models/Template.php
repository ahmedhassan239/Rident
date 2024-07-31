<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Template extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $fillable = [
        'name', 'slug', 'description', 'seo_title', 'seo_keywords', 'seo_description', 'banner','robots',
    ];
    public $translatable = ['name', 'slug', 'description', 'seo_title', 'seo_keywords', 'seo_description'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($template) {
            // Detach associated files before deleting the blog
            $template->files()->detach();
        });
    }
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
}
