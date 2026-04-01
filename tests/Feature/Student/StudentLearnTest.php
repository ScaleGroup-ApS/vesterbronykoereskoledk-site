<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\Student;
use App\Models\StudentPageProgress;
use App\Models\User;

function createEnrolledStudent(Offer $offer, EnrollmentStatus $status = EnrollmentStatus::Completed): Student
{
    $user = User::factory()->student()->create();
    $student = Student::factory()->for($user)->create();
    Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'status' => $status,
    ]);

    return $student;
}

test('student with completed enrollment can view a page', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    $this->actingAs($student->user)
        ->get(route('student.learn.page', [$offer, $module, $page]))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->component('student/learn/show')
            ->where('page.id', $page->id)
        );
});

test('student with pending enrollment gets 403', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();
    $student = createEnrolledStudent($offer, EnrollmentStatus::PendingApproval);

    $this->actingAs($student->user)
        ->get(route('student.learn.page', [$offer, $module, $page]))
        ->assertForbidden();
});

test('student without enrollment gets 403', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.learn.page', [$offer, $module, $page]))
        ->assertForbidden();
});

test('toc contains all modules and pages', function () {
    $offer = Offer::factory()->create();
    $module1 = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $module2 = OfferModule::factory()->for($offer)->create(['sort_order' => 1]);
    $page1 = OfferPage::factory()->for($module1, 'module')->create(['sort_order' => 0]);
    $page2 = OfferPage::factory()->for($module2, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    $this->actingAs($student->user)
        ->get(route('student.learn.page', [$offer, $module1, $page1]))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->has('modules', 2)
            ->where('modules.0.id', $module1->id)
            ->where('modules.1.id', $module2->id)
        );
});

test('completed page ids reflect student progress', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();
    $student = createEnrolledStudent($offer);

    StudentPageProgress::factory()->create([
        'student_id' => $student->id,
        'offer_page_id' => $page->id,
        'completed_at' => now(),
    ]);

    $this->actingAs($student->user)
        ->get(route('student.learn.page', [$offer, $module, $page]))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->where('completedPageIds', [$page->id])
        );
});

test('marking page complete records progress with timestamp', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();
    $student = createEnrolledStudent($offer);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.complete', [$offer, $module, $page]))
        ->assertRedirect();

    $progress = StudentPageProgress::where('student_id', $student->id)
        ->where('offer_page_id', $page->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->completed_at)->not->toBeNull();
});

test('marking page complete is idempotent', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();
    $student = createEnrolledStudent($offer);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.complete', [$offer, $module, $page]));

    $this->actingAs($student->user)
        ->post(route('student.learn.page.complete', [$offer, $module, $page]));

    expect(StudentPageProgress::where('student_id', $student->id)->count())->toBe(1);
});

test('marking complete redirects to next page', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page1 = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $page2 = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 1]);
    $student = createEnrolledStudent($offer);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.complete', [$offer, $module, $page1]))
        ->assertRedirect(route('student.learn.page', [$offer, $module, $page2]));
});

test('marking last page complete stays on same page', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.complete', [$offer, $module, $page]))
        ->assertRedirect(route('student.learn.page', [$offer, $module, $page]));
});
