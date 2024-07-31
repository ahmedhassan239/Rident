<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Review extends Model
{
    use HasFactory , HasTranslations;
    protected $fillable = [
        'name',
        'description',
        'link_name',
        'link','status','rate'
    ];
    public $translatable = [
        'name', 'description','link_name'
    ];
  
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
}
