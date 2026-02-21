<?php

use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentMediaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('students', StudentController::class);

    Route::post('students/{student}/media', [StudentMediaController::class, 'store'])->name('students.media.store');
    Route::get('students/{student}/media/{media}', [StudentMediaController::class, 'show'])->name('students.media.show');
    Route::delete('students/{student}/media/{media}', [StudentMediaController::class, 'destroy'])->name('students.media.destroy');
});

require __DIR__.'/settings.php';
