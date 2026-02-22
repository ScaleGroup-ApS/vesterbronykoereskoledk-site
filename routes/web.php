<?php

use App\Http\Controllers\Blog\BlogPostController;
use App\Http\Controllers\Bookings\BookingController;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Offers\OfferController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Progression\ProgressionController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Vehicles\VehicleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Offer;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'offers' => Offer::all(),
    ]);
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

// Public blog show (no auth)
Route::get('blog/{slug}', [BlogPostController::class, 'show'])->name('blog.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('blog', BlogPostController::class)->except(['show']);
    Route::resource('students', StudentController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::resource('offers', OfferController::class)->except(['show']);
    Route::resource('bookings', BookingController::class)->except(['show', 'edit']);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('students/{student}/progression', [ProgressionController::class, 'show'])->name('students.progression.show');

    Route::post('students/{student}/media', [StudentMediaController::class, 'store'])->name('students.media.store');
    Route::get('students/{student}/media/{media}', [StudentMediaController::class, 'show'])->name('students.media.show');
    Route::delete('students/{student}/media/{media}', [StudentMediaController::class, 'destroy'])->name('students.media.destroy');

    Route::get('chat', [ConversationController::class, 'index'])->name('chat.index');
    Route::post('chat', [ConversationController::class, 'store'])->name('chat.store');
    Route::get('chat/{conversation}/messages', [MessageController::class, 'index'])->name('chat.messages.index');
    Route::post('chat/{conversation}/messages', [MessageController::class, 'store'])->name('chat.messages.store');
    Route::get('chat/{conversation}/stream', [MessageController::class, 'stream'])->name('chat.messages.stream');
});

require __DIR__.'/settings.php';
