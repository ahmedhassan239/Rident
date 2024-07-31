<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PrivacyPolicy extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'privacy_policy';

    protected $fillable = ['title', 'description'];

    public $translatable = [
        'title', 'description'
    ];

}