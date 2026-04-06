<?php

use App\Http\Controllers\Auth\RequestMagicLinkController;
use App\Http\Controllers\Bookings\BookingAttendanceController;
use App\Http\Controllers\Bookings\BookingDayController;
use App\Http\Controllers\Bookings\BookingNoteController;
use App\Http\Controllers\Bookings\BookingSkillsController;
use App\Http\Controllers\Chat\ConversationMemberController;
use App\Http\Controllers\Chat\MessageAttachmentController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Courses\CourseAttendanceController;
use App\Http\Controllers\Courses\CourseSessionController;
use App\Http\Controllers\Curriculum\CurriculumMaterialUnlockController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\Marketing\ContactInquiryController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Offers\OfferMediaController;
use App\Http\Controllers\Offers\OfferPageBannerController;
use App\Http\Controllers\Offers\OfferPageMediaController;
use App\Http\Controllers\Offers\OfferPageQuizController;
use App\Http\Controllers\Offers\OfferPageVideoController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Student\BookingFeedbackController;
use App\Http\Controllers\Student\BulkStudentLoginLinkController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentLearnController;
use App\Http\Controllers\Student\StudentNotificationController;
use App\Http\Controllers\Student\StudentQuizAttemptController;
use App\Http\Controllers\Student\TheoryPracticeController;
use App\Http\Controllers\Students\StudentSkillController;
use App\Http\Controllers\TicketController;
use App\Livewire\Student\LearnPage;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('fordele', [MarketingController::class, 'features'])->name('marketing.features');
Route::get('pakker', [MarketingController::class, 'packages'])->name('marketing.packages');
Route::get('pakker/{offer}', [MarketingController::class, 'packageShow'])->name('marketing.packages.show');
Route::get('faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('vores-korelaerere', [MarketingController::class, 'instructors'])->name('marketing.instructors');
Route::get('til-elever/{slug}', [MarketingController::class, 'forStudents'])->name('marketing.for-students.show');
Route::get('om-os', [MarketingController::class, 'about'])->name('marketing.about');
Route::get('kontakt', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('kontakt', ContactInquiryController::class)
    ->middleware('throttle:5,1')
    ->name('marketing.contact.store');
Route::get('handelsbetingelser', [MarketingController::class, 'terms'])->name('marketing.terms');
Route::get('privatlivspolitik', [MarketingController::class, 'privacy'])->name('marketing.privacy');
Route::get('cookiepolitik', [MarketingController::class, 'cookies'])->name('marketing.cookies');

Route::post('login/magic-link', RequestMagicLinkController::class)
    ->middleware('throttle:5,1')
    ->name('login.magic-link');

Route::get('dashboard', fn () => redirect('/admin'))->middleware(['auth'])->name('dashboard');

Route::get('book/return', [EnrollmentController::class, 'stripeReturn'])->name('enrollment.stripe-return')->middleware('auth');
Route::get('book/{offer}', [EnrollmentController::class, 'show'])->name('enrollment.show');
Route::post('book/{offer}', [EnrollmentController::class, 'store'])->name('enrollment.store')->middleware(HandlePrecognitiveRequests::class);

Route::middleware(['auth', 'verified'])->group(function () {
    // Student learn (offer module pages — Blade/Livewire)
    Route::prefix('offers/{offer}/learn')->middleware('role:student')->name('student.learn.')->group(function () {
        Route::get('{module}', [StudentLearnController::class, 'redirectToFirstPage'])->name('module');
        Route::get('{module}/{page}', LearnPage::class)->name('page');
        Route::post('{module}/{page}/quiz', [StudentQuizAttemptController::class, 'store'])->name('page.quiz.attempt');
    });

    // Student panel mutation routes (Filament pages pending for GET equivalents)
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::post('/teoritraening', [TheoryPracticeController::class, 'store'])->name('theory-practice.store');
        Route::get('/teoritraening/{attempt}', [TheoryPracticeController::class, 'result'])->name('theory-practice.result');

        Route::post('/notifikationer/{id}/read', [StudentNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifikationer/read-all', [StudentNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

        Route::post('/feedback/{booking}', [BookingFeedbackController::class, 'store'])->name('feedback.store');
    });

    Route::get('student/offers/{offer}/materials/{media}', [MediaController::class, 'show'])
        ->middleware('role:student')
        ->name('student.offers.materials.show');

    Route::get('student/offers/{offer}/pages/{page}/media/{media}', [MediaController::class, 'show'])
        ->middleware('role:student')
        ->name('student.offers.pages.media.show');

    // Media routes
    Route::post('offers/{offer}/media', [OfferMediaController::class, 'store'])->name('offers.media.store');
    Route::get('offers/{offer}/media/{media}', [MediaController::class, 'show'])->name('offers.media.show');
    Route::delete('offers/{offer}/media/{media}', [OfferMediaController::class, 'destroy'])->name('offers.media.destroy');
    Route::post('offers/{offer}/modules/{module}/pages/{page}/media', [OfferPageMediaController::class, 'store'])->name('offers.modules.pages.media.store');
    Route::get('offers/{offer}/modules/{module}/pages/{page}/media/{media}', [MediaController::class, 'show'])->name('offers.modules.pages.media.show');
    Route::delete('offers/{offer}/modules/{module}/pages/{page}/media/{media}', [MediaController::class, 'destroy'])->name('offers.modules.pages.media.destroy');
    Route::post('offers/{offer}/modules/{module}/pages/{page}/banner', [OfferPageBannerController::class, 'store'])->name('offers.modules.pages.banner.store');
    Route::get('offers/{offer}/modules/{module}/pages/{page}/banner', [OfferPageBannerController::class, 'show'])->name('offers.modules.pages.banner.show');
    Route::delete('offers/{offer}/modules/{module}/pages/{page}/banner', [OfferPageBannerController::class, 'destroy'])->name('offers.modules.pages.banner.destroy');
    Route::post('offers/{offer}/modules/{module}/pages/{page}/video', [OfferPageVideoController::class, 'store'])->name('offers.modules.pages.video.store');
    Route::get('offers/{offer}/modules/{module}/pages/{page}/video/{media}', [MediaController::class, 'show'])->name('offers.modules.pages.video.show');
    Route::delete('offers/{offer}/modules/{module}/pages/{page}/video/{media}', [MediaController::class, 'destroy'])->name('offers.modules.pages.video.destroy');

    Route::resource('offers.modules.pages.questions', OfferPageQuizController::class)->only(['store', 'update', 'destroy']);

    Route::patch('courses/{course}/enrollments/{enrollment}/attendance', CourseAttendanceController::class)
        ->name('courses.enrollments.attendance');

    Route::post('courses/{course}/sessions/{session}/cancel', [CourseSessionController::class, 'cancel'])
        ->name('courses.sessions.cancel');
    Route::patch('courses/{course}/sessions/{session}/attendance', [CourseSessionController::class, 'attendance'])
        ->name('courses.sessions.attendance');

    Route::get('bookings/day/{date}', BookingDayController::class)->name('bookings.day');
    Route::post('bookings/{booking}/attendance', BookingAttendanceController::class)
        ->name('bookings.attendance.store');
    Route::patch('bookings/{booking}/note', BookingNoteController::class)
        ->name('bookings.note');
    Route::patch('bookings/{booking}/skills', BookingSkillsController::class)
        ->name('bookings.skills');

    Route::patch('students/{student}/skills', StudentSkillController::class)->name('students.skills');
    Route::post('students/{student}/login-link', [StudentController::class, 'sendLoginLink'])->name('students.login-link');
    Route::post('students/bulk-login-links', BulkStudentLoginLinkController::class)->name('students.bulk-login-links');
    Route::post('students/{student}/media', [StudentController::class, 'storeMedia'])->name('students.media.store');
    Route::get('students/{student}/media/{media}', [MediaController::class, 'show'])->name('students.media.show');
    Route::delete('students/{student}/media/{media}', [MediaController::class, 'destroy'])->name('students.media.destroy');

    Route::get('chat/{conversation}/messages', [MessageController::class, 'index'])->name('chat.messages.index');
    Route::post('chat/{conversation}/messages', [MessageController::class, 'store'])->name('chat.messages.store');
    Route::get('chat/{conversation}/stream', [MessageController::class, 'stream'])->name('chat.messages.stream');
    Route::get('chat/{conversation}/messages/{message}/attachments/{media}', [MessageAttachmentController::class, 'show'])->name('chat.messages.attachments.show');
    Route::post('chat/{conversation}/members', [ConversationMemberController::class, 'store'])->name('chat.members.store');
    Route::delete('chat/{conversation}/members/{user}', [ConversationMemberController::class, 'destroy'])->name('chat.members.destroy');

    Route::patch('offers/{offer}/curriculum/materials', CurriculumMaterialUnlockController::class)
        ->name('curriculum.materials.unlock');

    Route::middleware('role:admin,instructor')->group(function () {
        Route::get('support', [TicketController::class, 'index'])->name('support.index');
        Route::post('support', [TicketController::class, 'store'])->name('support.store');
        Route::get('support/{ticketId}', [TicketController::class, 'show'])->name('support.show');
        Route::post('support/{ticketId}/comments', [TicketController::class, 'addComment'])->name('support.comment');
    });

    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');
});
