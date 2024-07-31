<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TermsAndCondition extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'terms_and_condition';

    protected $fillable = ['title', 'description'];

    public $translatable = [
        'title', 'description'
    ];
}
