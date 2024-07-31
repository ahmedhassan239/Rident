<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = ['title', 'description', 'service_id'];

    public $translatable = [
        'title', 'description'
    ];

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
