<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/api', function () {
    return response()->json(['status' => 'success']);
});

Route::resource('api/books', BookController::class);
Route::resource('api/reviews', ReviewController::class);
