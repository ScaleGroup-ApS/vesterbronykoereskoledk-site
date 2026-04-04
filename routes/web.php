<?php

use App\Http\Controllers\Blog\BlogPostController;
use App\Http\Controllers\Bookings\BookingAttendanceController;
use App\Http\Controllers\Bookings\BookingController;
use App\Http\Controllers\Bookings\BookingDayController;
use App\Http\Controllers\Bookings\BookingNoteController;
use App\Http\Controllers\Bookings\BookingSkillsController;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\ConversationMemberController;
use App\Http\Controllers\Chat\MessageAttachmentController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Courses\CourseAttendanceController;
use App\Http\Controllers\Curriculum\CurriculumMaterialUnlockController;
use App\Http\Controllers\Curriculum\CurriculumTopicController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Enrollment\EnrollmentApprovalController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\Marketing\Admin\MarketingHomeCopyController;
use App\Http\Controllers\Marketing\Admin\MarketingTestimonialController;
use App\Http\Controllers\Marketing\Admin\MarketingValueBlockController;
use App\Http\Controllers\Marketing\ContactInquiryController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Offers\CourseController;
use App\Http\Controllers\Offers\OfferController;
use App\Http\Controllers\Offers\OfferMediaController;
use App\Http\Controllers\Offers\OfferModuleController;
use App\Http\Controllers\Offers\OfferPageBannerController;
use App\Http\Controllers\Offers\OfferPageController;
use App\Http\Controllers\Offers\OfferPageMediaController;
use App\Http\Controllers\Offers\OfferPageQuizController;
use App\Http\Controllers\Offers\OfferPageVideoController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Progression\ProgressionController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Student\BulkStudentLoginLinkController;
use App\Http\Controllers\Student\StudentCalendarController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentLearnController;
use App\Http\Controllers\Student\StudentQuizAttemptController;
use App\Http\Controllers\Students\StudentSkillController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\TicketController;
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
Route::get('til-elever/{slug}', [MarketingController::class, 'forStudents'])->name('marketing.for-students.show');
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
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/forloeb', [StudentDashboardController::class, 'progress'])->name('progress');
        Route::get('/historik', [StudentDashboardController::class, 'history'])->name('history');
        Route::get('/materiale', [StudentDashboardController::class, 'materials'])->name('materials');
        Route::get('/faerdigheder', [StudentDashboardController::class, 'skills'])->name('skills');
    });

    Route::get('student/kalender', StudentCalendarController::class)
        ->middleware('role:student')
        ->name('student.calendar');

    Route::get('student/offers/{offer}/materials/{media}', [MediaController::class, 'show'])
        ->middleware('role:student')
        ->name('student.offers.materials.show');

    Route::get('student/offers/{offer}/pages/{page}/media/{media}', [MediaController::class, 'show'])
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
    Route::resource('offers.courses', CourseController::class)
        ->only(['store', 'destroy']);

    // Instructor module + page authoring
    Route::resource('offers.modules', OfferModuleController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::post('offers/{offer}/modules/{module}/move-up', [OfferModuleController::class, 'moveUp'])->name('offers.modules.move-up');
    Route::post('offers/{offer}/modules/{module}/move-down', [OfferModuleController::class, 'moveDown'])->name('offers.modules.move-down');

    Route::resource('offers.modules.pages', OfferPageController::class)->only(['store', 'edit', 'update', 'destroy']);
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
    Route::post('offers/{offer}/modules/{module}/pages/{page}/move-up', [OfferPageController::class, 'moveUp'])->name('offers.modules.pages.move-up');
    Route::post('offers/{offer}/modules/{module}/pages/{page}/move-down', [OfferPageController::class, 'moveDown'])->name('offers.modules.pages.move-down');

    Route::resource('offers.modules.pages.questions', OfferPageQuizController::class)->only(['store', 'update', 'destroy']);
    Route::get('courses', [App\Http\Controllers\Courses\CourseController::class, 'index'])->name('courses.index');
    Route::post('courses', [App\Http\Controllers\Courses\CourseController::class, 'store'])->name('courses.store');
    Route::get('courses/{course}', [App\Http\Controllers\Courses\CourseController::class, 'show'])->name('courses.show');
    Route::patch('courses/{course}', [App\Http\Controllers\Courses\CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [App\Http\Controllers\Courses\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::patch('courses/{course}/enrollments/{enrollment}/attendance', CourseAttendanceController::class)
        ->name('courses.enrollments.attendance');
    Route::get('bookings/day/{date}', BookingDayController::class)->name('bookings.day');
    Route::post('bookings/{booking}/attendance', BookingAttendanceController::class)
        ->name('bookings.attendance.store');
    Route::patch('bookings/{booking}/note', BookingNoteController::class)
        ->name('bookings.note');
    Route::patch('bookings/{booking}/skills', BookingSkillsController::class)
        ->name('bookings.skills');
    Route::resource('bookings', BookingController::class)->except(['show', 'edit']);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('students/{student}/progression', [ProgressionController::class, 'show'])->name('students.progression.show');

    Route::patch('students/{student}/skills', StudentSkillController::class)->name('students.skills');
    Route::post('students/{student}/login-link', [StudentController::class, 'sendLoginLink'])->name('students.login-link');
    Route::post('students/bulk-login-links', BulkStudentLoginLinkController::class)->name('students.bulk-login-links');
    Route::post('students/{student}/media', [StudentController::class, 'storeMedia'])->name('students.media.store');
    Route::get('students/{student}/media/{media}', [MediaController::class, 'show'])->name('students.media.show');
    Route::delete('students/{student}/media/{media}', [MediaController::class, 'destroy'])->name('students.media.destroy');

    Route::get('chat', [ConversationController::class, 'index'])->name('chat.index');
    Route::post('chat', [ConversationController::class, 'store'])->name('chat.store');
    Route::get('chat/{conversation}/messages', [MessageController::class, 'index'])->name('chat.messages.index');
    Route::post('chat/{conversation}/messages', [MessageController::class, 'store'])->name('chat.messages.store');
    Route::get('chat/{conversation}/stream', [MessageController::class, 'stream'])->name('chat.messages.stream');
    Route::get('chat/{conversation}/messages/{message}/attachments/{media}', [MessageAttachmentController::class, 'show'])->name('chat.messages.attachments.show');
    Route::post('chat/{conversation}/members', [ConversationMemberController::class, 'store'])->name('chat.members.store');
    Route::delete('chat/{conversation}/members/{user}', [ConversationMemberController::class, 'destroy'])->name('chat.members.destroy');

    Route::get('enrollments', [EnrollmentApprovalController::class, 'index'])->name('enrollments.index');
    Route::post('enrollments/{enrollment}/approve', [EnrollmentApprovalController::class, 'approve'])->name('enrollments.approve');
    Route::post('enrollments/{enrollment}/reject', [EnrollmentApprovalController::class, 'reject'])->name('enrollments.reject');

    Route::middleware('role:admin,instructor')->group(function () {
        Route::get('support', [TicketController::class, 'index'])->name('support.index');
        Route::post('support', [TicketController::class, 'store'])->name('support.store');
        Route::get('support/{ticketId}', [TicketController::class, 'show'])->name('support.show');
        Route::post('support/{ticketId}/comments', [TicketController::class, 'addComment'])->name('support.comment');
    });

    Route::get('timeline', TimelineController::class)->middleware('role:admin')->name('timeline.index');

    Route::resource('staff', StaffController::class)
        ->only(['index', 'create', 'store'])
        ->middleware('role:admin');

    Route::get('offers/{offer}/curriculum', [CurriculumTopicController::class, 'index'])
        ->name('curriculum.index');
    Route::post('offers/{offer}/curriculum', [CurriculumTopicController::class, 'store'])
        ->name('curriculum.store');
    Route::put('curriculum/{topic}', [CurriculumTopicController::class, 'update'])
        ->name('curriculum.update');
    Route::delete('curriculum/{topic}', [CurriculumTopicController::class, 'destroy'])
        ->name('curriculum.destroy');
    Route::patch('offers/{offer}/curriculum/materials', CurriculumMaterialUnlockController::class)
        ->name('curriculum.materials.unlock');

    Route::middleware('role:admin')->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('home-copy', [MarketingHomeCopyController::class, 'edit'])->name('home-copy.edit');
        Route::put('home-copy', [MarketingHomeCopyController::class, 'update'])->name('home-copy.update');
        Route::resource('value-blocks', MarketingValueBlockController::class)->except(['show']);
        Route::resource('testimonials', MarketingTestimonialController::class)->except(['show']);
    });
});

require __DIR__.'/settings.php';
