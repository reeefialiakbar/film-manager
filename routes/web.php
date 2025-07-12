<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// مسیرهای فیلم‌ها
Route::prefix('movies')->group(function () {
    Route::get('/iranian', function () {
        return view('movies.iranian');
    });
    Route::get('/foreign', function () {
        return view('movies.foreign');
    });
    Route::get('/animation', function () {
        return view('movies.animation');
    });
    Route::get('/trailer', function () {
        return view('movies.trailer');
    });
});

// مسیرهای سریال‌ها
Route::prefix('series')->group(function () {
    Route::get('/iranian', function () {
        return view('series.iranian');
    });
    Route::get('/foreign', function () {
        return view('series.foreign');
    });
    Route::get('/animation', function () {
        return view('series.animation');
    });
    Route::get('/trailer', function () {
        return view('series.trailer');
    });
});

Route::get('/settings', function () {
    return view('settings');
});
// مسیرهای قبلی
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// مسیرهای فیلم‌ها
Route::get('/movies/iranian', [MovieController::class, 'iranianIndex']);
Route::post('/movies', [MovieController::class, 'store']);
