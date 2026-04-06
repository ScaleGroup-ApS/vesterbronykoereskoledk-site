<?php

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\OfferPageQuizQuestion;
use App\Models\Student;
use App\Models\TheoryPracticeAttempt;
use App\Models\User;

it('student can submit theory practice answers', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $offer = Offer::factory()->create();
    $student->offers()->attach($offer);

    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $questions = OfferPageQuizQuestion::factory()->for($page, 'page')->count(3)->create();

    $this->actingAs($user)
        ->post(route('student.theory-practice.store'), [
            'answers' => [0, 1, 2],
            'question_ids' => $questions->pluck('id')->toArray(),
            'duration_seconds' => 120,
        ])
        ->assertRedirect();

    expect(TheoryPracticeAttempt::where('student_id', $student->id)->count())->toBe(1);
});

it('student cannot view another students theory practice result', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $otherStudent = Student::factory()->create();
    $attempt = TheoryPracticeAttempt::factory()->for($otherStudent)->create();

    $this->actingAs($user)
        ->get(route('student.theory-practice.result', $attempt))
        ->assertForbidden();
});
