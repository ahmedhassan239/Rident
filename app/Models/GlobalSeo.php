<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class GlobalSeo extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'globalseo';
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($globalseo) {
            // Detach associated files before deleting the blog
            $globalseo->files()->detach();
        });
    }
    protected $fillable = [
         'title', 'keywords',
          'description', 'facebook_description',
          'facebook_image', 'twitter_title', 'twitter_description',
           'twitter_image', 'revisit_after',
            'facebook_page_id',
             'author', 
             'robots', 
              'google_site_verification' ,'facebook_site_name',
                'facebook_admins', 'twitter_site', 'twitter_card', 'og_type', 'og_title', 'seo_schema',
                 'og_url', 'twitter_label1', 'twitter_data1'
    ];
    public $translatable = [
        'title', 'keywords',
                'description', 'facebook_description',
                 'twitter_title', 'twitter_description', 'twitter_card'
                 , 'og_title', 'seo_schema', 'twitter_label1', 'twitter_data1'
    ];

    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
    
}
