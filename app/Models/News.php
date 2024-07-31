<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class News extends Model
{
    use HasFactory;
    use HasTranslations;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($news) {
            // Detach associated files before deleting the blog
            $news->files()->detach();
        });
    }

        // Specify the fields that are mass assignable
        protected $fillable = [
            'name',
            'slug',
            'description',
            'link',
            'status',
          
        ];
        public $translatable = [
            'name', 'slug', 'description',
        ];
    
        // If you need to cast related_blogs as an array
    
    
        public function files()
        {
            return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
        }
}
