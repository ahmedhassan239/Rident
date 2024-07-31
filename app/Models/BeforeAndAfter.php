<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BeforeAndAfter extends Model
{
    use HasFactory,HasTranslations;
    protected $table = 'before_and_after'; 
    protected $fillable = [
        'description', 'status','featured','service_id'
    ];
    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files')->withPivot('type');
    }
    public $translatable = [
        'description'
    ];

}
