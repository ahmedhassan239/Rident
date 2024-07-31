<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Video extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $fillable = [
        'title',
        'link',
        'playlist_id',
    ];
    public $translatable = [
        'title'
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}
