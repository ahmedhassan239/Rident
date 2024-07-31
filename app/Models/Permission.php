<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'guard_name'];
    protected $attributes = [
        'guard_name' => 'api', // Set the default guard here
    ];
}
