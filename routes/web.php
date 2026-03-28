<?php

use App\Http\Controllers\Blog\BlogPostController;
use App\Http\Controllers\Bookings\BookingAttendanceController;
use App\Http\Controllers\Bookings\BookingController;
use App\Http\Controllers\Bookings\BookingNoteController;
use App\Http\Controllers\Bookings\BookingSkillsController;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Enrollment\EnrollmentApprovalController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\Marketing\Admin\MarketingHomeCopyController;
use App\Http\Controllers\Marketing\Admin\MarketingTestimonialController;
use App\Http\Controllers\Marketing\Admin\MarketingValueBlockController;
use App\Http\Controllers\Marketing\ContactInquiryController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Offers\OfferController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Progression\ProgressionController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentForloebController;
use App\Http\Controllers\Student\StudentOfferMaterialController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentLoginLinkController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Timeline\TimelineController;
use App\Http\Controllers\Vehicles\VehicleController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('fordele', [MarketingController::class, 'features'])->name('marketing.features');
Route::get('pakker', [MarketingController::class, 'packages'])->name('marketing.packages');
Route::get('pakker/{offer}', [MarketingController::class, 'packageShow'])->name('marketing.packages.show');
Route::get('faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('vores-korelaerere', [MarketingController::class, 'instructors'])->name('marketing.instructors');
Route::get('til-elever/{slug}', [MarketingController::class, 'tilElever'])->name('marketing.til-elever.show');
Route::get('om-os', [MarketingController::class, 'about'])->name('marketing.about');
Route::get('kontakt', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('kontakt', ContactInquiryController::class)
    ->middleware('throttle:5,1')
    ->name('marketing.contact.store');
Route::get('handelsbetingelser', [MarketingController::class, 'terms'])->name('marketing.terms');
Route::get('privatlivspolitik', [MarketingController::class, 'privacy'])->name('marketing.privacy');
Route::get('cookiepolitik', [MarketingController::class, 'cookies'])->name('marketing.cookies');

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

    Route::get('student/forloeb', StudentForloebController::class)
        ->middleware('role:student')
        ->name('student.forloeb');

    Route::get('student/offers/{offer}/materials/{media}', StudentOfferMaterialController::class)
        ->middleware('role:student')
        ->name('student.offers.materials.show');

    Route::resource('blog', BlogPostController::class)->except(['show']);
    Route::resource('students', StudentController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::resource('offers', OfferController::class)->except(['show']);
    Route::get('courses', [\App\Http\Controllers\Courses\CourseController::class, 'index'])->name('courses.index');
    Route::post('courses', [\App\Http\Controllers\Courses\CourseController::class, 'store'])->name('courses.store');
    Route::get('courses/{course}', [\App\Http\Controllers\Courses\CourseController::class, 'show'])->name('courses.show');
    Route::patch('courses/{course}', [\App\Http\Controllers\Courses\CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [\App\Http\Controllers\Courses\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('bookings/day/{date}', \App\Http\Controllers\Bookings\BookingDayController::class)->name('bookings.day');
    Route::post('bookings/{booking}/attendance', BookingAttendanceController::class)
        ->name('bookings.attendance.store');
    Route::patch('bookings/{booking}/note', BookingNoteController::class)
        ->name('bookings.note');
    Route::patch('bookings/{booking}/skills', BookingSkillsController::class)
        ->name('bookings.skills');
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

    Route::middleware('role:admin')->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('home-copy', [MarketingHomeCopyController::class, 'edit'])->name('home-copy.edit');
        Route::put('home-copy', [MarketingHomeCopyController::class, 'update'])->name('home-copy.update');
        Route::resource('value-blocks', MarketingValueBlockController::class)->except(['show']);
        Route::resource('testimonials', MarketingTestimonialController::class)->except(['show']);
    });
});

require __DIR__.'/settings.php';
