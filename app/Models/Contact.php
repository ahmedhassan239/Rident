<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Contact extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $table = 'contacts';

    // Specify the fields that can be mass-assigned
    protected $fillable = [
        'address1', 'address2', 'phone1', 'phone2', 'location1', 'location2',
        'email1', 'email2', 'facebook', 'linkedin', 'twitter', 'snapchat', 
        'instagram', 'youtube'
    ];
    public $translatable = [
        'address1', 'address2', 'phone1', 'phone2', 'location1', 'location2',
        'email1', 'email2', 'facebook', 'linkedin', 'twitter', 'snapchat', 
        'instagram', 'youtube'
    ];
}
