<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function iranianIndex()
    {
        $movies = Movie::where('category', 'iranian')->get();
        return view('movies.iranian', compact('movies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'file_path' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('movies', 'public');
            $data['image'] = $imagePath;
        }

        $data['category'] = 'iranian';
        Movie::create($data);

        return response()->json(['success' => true]);
    }
}
