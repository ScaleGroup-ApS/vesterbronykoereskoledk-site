<?php

use App\Http\Controllers\Blog\BlogPostController;
use App\Http\Controllers\Bookings\BookingController;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Enrollment\EnrollmentApprovalController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Offers\OfferController;
use App\Http\Controllers\Offers\OfferModuleController;
use App\Http\Controllers\Offers\OfferPageController;
use App\Http\Controllers\Offers\OfferPageQuizController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Progression\ProgressionController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentLearnController;
use App\Http\Controllers\Student\StudentOfferMaterialController;
use App\Http\Controllers\Student\StudentPageMediaController;
use App\Http\Controllers\Student\StudentQuizAttemptController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentLoginLinkController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Timeline\TimelineController;
use App\Http\Controllers\Vehicles\VehicleController;
use App\Models\Offer;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'offers' => Offer::all(),
    ]);
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('book/return', [EnrollmentController::class, 'stripeReturn'])->name('enrollment.stripe-return')->middleware('auth');
Route::get('book/{offer}', [EnrollmentController::class, 'show'])->name('enrollment.show');
Route::post('book/{offer}', [EnrollmentController::class, 'store'])->name('enrollment.store')->middleware(HandlePrecognitiveRequests::class);

// Public blog show (no auth)
Route::get('blog/{slug}', [BlogPostController::class, 'show'])->name('blog.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('student', StudentDashboardController::class)
        ->middleware('role:student')
        ->name('student.dashboard');

    Route::get('student/offers/{offer}/materials/{media}', StudentOfferMaterialController::class)
        ->middleware('role:student')
        ->name('student.offers.materials.show');

    Route::get('student/offers/{offer}/pages/{page}/media/{media}', StudentPageMediaController::class)
        ->middleware('role:student')
        ->name('student.offers.pages.media.show');

    // Student learn
    Route::prefix('offers/{offer}/learn')->middleware('role:student')->name('student.learn.')->group(function () {
        Route::get('{module}/{page?}', [StudentLearnController::class, 'show'])->name('page');
        Route::post('{module}/{page}/complete', [StudentLearnController::class, 'markComplete'])->name('page.complete');
        Route::post('{module}/{page}/quiz', [StudentQuizAttemptController::class, 'store'])->name('page.quiz.attempt');
    });

    Route::resource('blog', BlogPostController::class)->except(['show']);
    Route::resource('students', StudentController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::resource('offers', OfferController::class)->except(['show']);
    Route::resource('offers.courses', \App\Http\Controllers\Offers\CourseController::class)
        ->only(['store', 'destroy']);

    // Instructor module + page authoring
    Route::resource('offers.modules', OfferModuleController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::post('offers/{offer}/modules/{module}/move-up', [OfferModuleController::class, 'moveUp'])->name('offers.modules.move-up');
    Route::post('offers/{offer}/modules/{module}/move-down', [OfferModuleController::class, 'moveDown'])->name('offers.modules.move-down');

    Route::resource('offers.modules.pages', OfferPageController::class)->only(['store', 'edit', 'update', 'destroy']);
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::post('offers/{offer}/modules/{module}/pages/{page}/move-up', [OfferPageController::class, 'moveUp'])->name('offers.modules.pages.move-up');
    Route::post('offers/{offer}/modules/{module}/pages/{page}/move-down', [OfferPageController::class, 'moveDown'])->name('offers.modules.pages.move-down');

    Route::resource('offers.modules.pages.questions', OfferPageQuizController::class)->only(['store', 'update', 'destroy']);
    Route::get('courses', [\App\Http\Controllers\Courses\CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [\App\Http\Controllers\Courses\CourseController::class, 'show'])->name('courses.show');
    Route::patch('courses/{course}', [\App\Http\Controllers\Courses\CourseController::class, 'update'])->name('courses.update');
    Route::get('bookings/day/{date}', \App\Http\Controllers\Bookings\BookingDayController::class)->name('bookings.day');
    Route::resource('bookings', BookingController::class)->except(['show', 'edit']);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('students/{student}/progression', [ProgressionController::class, 'show'])->name('students.progression.show');

    Route::post('students/{student}/login-link', StudentLoginLinkController::class)->name('students.login-link');
    Route::post('students/{student}/media', [StudentMediaController::class, 'store'])->name('students.media.store');
    Route::get('students/{student}/media/{media}', [StudentMediaController::class, 'show'])->name('students.media.show');
    Route::delete('students/{student}/media/{media}', [StudentMediaController::class, 'destroy'])->name('students.media.destroy');

    Route::get('chat', [ConversationController::class, 'index'])->name('chat.index');
    Route::post('chat', [ConversationController::class, 'store'])->name('chat.store');
    Route::get('chat/{conversation}/messages', [MessageController::class, 'index'])->name('chat.messages.index');
    Route::post('chat/{conversation}/messages', [MessageController::class, 'store'])->name('chat.messages.store');
    Route::get('chat/{conversation}/stream', [MessageController::class, 'stream'])->name('chat.messages.stream');

    Route::get('enrollments', [EnrollmentApprovalController::class, 'index'])->name('enrollments.index');
    Route::post('enrollments/{enrollment}/approve', [EnrollmentApprovalController::class, 'approve'])->name('enrollments.approve');
    Route::post('enrollments/{enrollment}/reject', [EnrollmentApprovalController::class, 'reject'])->name('enrollments.reject');

    Route::get('timeline', TimelineController::class)->middleware('role:admin')->name('timeline.index');
});

require __DIR__.'/settings.php';
