<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function iranianIndex()
    {
        $movies = Movie::where('category', 'iranian')
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('movies.iranian', compact('movies'));
    }

public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'persian_title' => 'nullable|string',
        'file_path' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'release_date' => 'nullable|date',
        'imdb_rating' => 'nullable|numeric|min:0|max:10',
        'your_rating' => 'nullable|integer|min:0|max:10',
        'genres' => 'nullable|array'
    ]);

    $data = $request->all();

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('movies', 'public');
        $data['image'] = $imagePath;
    }

    $data['category'] = 'iranian';
    $data['genres'] = json_encode($request->genres);

    Movie::create($data);

    return response()->json(['success' => true]);
}
}
