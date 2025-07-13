<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'persian_title',
        'director',
        'release_date',
        'description',
        'image',
        'file_path',
        'category',
        'duration',
        'genres',
        'imdb_rating',
        'your_rating'
    ];

    protected $casts = [
        'genres' => 'array',
        'release_date' => 'date',
        'imdb_rating' => 'float',
        'your_rating' => 'integer'
    ];
}
