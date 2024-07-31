<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Playlist extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $fillable = [
        'name',
        'slug',
    ];
    public $translatable = [
        'name', 'slug', 
    ];
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
