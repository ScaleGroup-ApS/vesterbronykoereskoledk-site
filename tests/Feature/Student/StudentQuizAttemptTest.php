<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\OfferPageQuizQuestion;
use App\Models\Student;
use App\Models\StudentQuizAttempt;
use App\Models\User;

function enrolledStudentForQuiz(Offer $offer, EnrollmentStatus $status = EnrollmentStatus::Completed): Student
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

test('student can submit quiz attempt and score is correct', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    OfferPageQuizQuestion::factory()->for($page, 'page')->create([
        'options' => ['A', 'B', 'C'],
        'correct_option' => 1,
        'sort_order' => 0,
    ]);
    OfferPageQuizQuestion::factory()->for($page, 'page')->create([
        'options' => ['X', 'Y'],
        'correct_option' => 0,
        'sort_order' => 1,
    ]);

    $student = enrolledStudentForQuiz($offer);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.quiz.attempt', [$offer, $module, $page]), [
            'answers' => [1, 0],
        ])
        ->assertRedirect();

    $attempt = StudentQuizAttempt::first();
    expect($attempt)->not->toBeNull();
    expect($attempt->score)->toBe(2);
    expect($attempt->total)->toBe(2);
    expect($attempt->answers)->toBe([1, 0]);
});

test('student can re-attempt quiz without unique conflict', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    OfferPageQuizQuestion::factory()->for($page, 'page')->create([
        'options' => ['A', 'B'],
        'correct_option' => 0,
        'sort_order' => 0,
    ]);

    $student = enrolledStudentForQuiz($offer);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.quiz.attempt', [$offer, $module, $page]), ['answers' => [0]]);

    $this->actingAs($student->user)
        ->post(route('student.learn.page.quiz.attempt', [$offer, $module, $page]), ['answers' => [1]]);

    expect(StudentQuizAttempt::count())->toBe(2);
});

test('non-enrolled student cannot submit quiz', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('student.learn.page.quiz.attempt', [$offer, $module, $page]), ['answers' => [0]])
        ->assertForbidden();
});

test('latest quiz attempt is included in page props', function () {
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    OfferPageQuizQuestion::factory()->for($page, 'page')->create([
        'options' => ['A', 'B'],
        'correct_option' => 0,
        'sort_order' => 0,
    ]);

    $student = enrolledStudentForQuiz($offer);

    StudentQuizAttempt::factory()->create([
        'student_id' => $student->id,
        'offer_page_id' => $page->id,
        'answers' => [0],
        'score' => 1,
        'total' => 1,
        'attempted_at' => now(),
    ]);

    Livewire\Livewire::actingAs($student->user)
        ->test(\App\Livewire\Student\LearnPage::class, ['offer' => $offer, 'module' => $module, 'page' => $page])
        ->assertSet('attempt.score', 1)
        ->assertSet('attempt.total', 1)
        ->assertSet('submitted', true);
});
