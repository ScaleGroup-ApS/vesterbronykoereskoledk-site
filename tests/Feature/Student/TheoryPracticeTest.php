<?php

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\OfferPageQuizQuestion;
use App\Models\Student;
use App\Models\TheoryPracticeAttempt;
use App\Models\User;

it('student can view theory practice index page', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.theory-practice'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/theory-practice')
            ->has('attempts')
            ->has('stats')
        );
});

it('theory practice shows attempt history', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    TheoryPracticeAttempt::factory()->for($student)->count(3)->create();

    $this->actingAs($user)
        ->get(route('student.theory-practice'))
        ->assertInertia(fn ($page) => $page
            ->has('attempts', 3)
            ->has('stats', fn ($stats) => $stats
                ->where('total_attempts', 3)
                ->etc()
            )
        );
});

it('student can start a theory practice exam when questions are available', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $offer = Offer::factory()->create();
    $student->offers()->attach($offer);

    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    OfferPageQuizQuestion::factory()->for($page, 'page')->count(5)->create();

    $this->actingAs($user)
        ->get(route('student.theory-practice.start'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/theory-practice-exam')
            ->has('questions')
            ->has('time_limit_seconds')
        );
});

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

it('student can view theory practice result', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $offer = Offer::factory()->create();
    $student->offers()->attach($offer);

    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $questions = OfferPageQuizQuestion::factory()->for($page, 'page')->count(3)->create();

    $attempt = TheoryPracticeAttempt::factory()->for($student)->create([
        'question_ids' => $questions->pluck('id')->toArray(),
        'answers' => [0, 1, 2],
        'score' => 2,
        'total' => 3,
    ]);

    $this->actingAs($user)
        ->get(route('student.theory-practice.result', $attempt))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/theory-practice-result')
            ->has('attempt')
            ->has('questions', 3)
        );
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
