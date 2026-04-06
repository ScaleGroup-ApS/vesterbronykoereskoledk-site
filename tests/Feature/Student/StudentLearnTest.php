<?php

use App\Enums\EnrollmentStatus;
use App\Livewire\Student\LearnPage;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\Student;
use App\Models\StudentPageProgress;
use App\Models\User;
use Livewire\Livewire;

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
        ->assertOk();
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
    OfferPage::factory()->for($module2, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    Livewire::actingAs($student->user)
        ->test(LearnPage::class, ['offer' => $offer, 'module' => $module1, 'page' => $page1])
        ->assertSet('modulesWithPages', fn ($modules) => count($modules) === 2
            && $modules[0]['id'] === $module1->id
            && $modules[1]['id'] === $module2->id
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

    Livewire::actingAs($student->user)
        ->test(LearnPage::class, ['offer' => $offer, 'module' => $module, 'page' => $page])
        ->assertSet('completedPageIds', [$page->id]);
});

test('marking page complete records progress with timestamp', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    Livewire::actingAs($student->user)
        ->test(LearnPage::class, ['offer' => $offer, 'module' => $module, 'page' => $page])
        ->call('markComplete');

    $progress = StudentPageProgress::where('student_id', $student->id)
        ->where('offer_page_id', $page->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->completed_at)->not->toBeNull();
});

test('marking page complete is idempotent', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    $component = Livewire::actingAs($student->user)
        ->test(LearnPage::class, ['offer' => $offer, 'module' => $module, 'page' => $page]);

    $component->call('markComplete');
    $component->call('markComplete');

    expect(StudentPageProgress::where('student_id', $student->id)->count())->toBe(1);
});

test('marking complete redirects to next page', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page1 = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $page2 = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 1]);
    $student = createEnrolledStudent($offer);

    Livewire::actingAs($student->user)
        ->test(LearnPage::class, ['offer' => $offer, 'module' => $module, 'page' => $page1])
        ->call('markComplete')
        ->assertRedirect(route('student.learn.page', [$offer, $module, $page2]));
});

test('marking last page complete stays on same page', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $page = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $student = createEnrolledStudent($offer);

    Livewire::actingAs($student->user)
        ->test(LearnPage::class, ['offer' => $offer, 'module' => $module, 'page' => $page])
        ->call('markComplete')
        ->assertRedirect(route('student.learn.page', [$offer, $module, $page]));
});
