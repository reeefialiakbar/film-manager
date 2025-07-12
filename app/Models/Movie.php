<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'director',
        'year',
        'description',
        'image',
        'file_path',
        'category',
        'duration'
    ];
}
