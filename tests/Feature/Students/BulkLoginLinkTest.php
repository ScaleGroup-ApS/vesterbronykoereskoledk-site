<?php

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;

test('admin can send bulk login links', function () {
    Mail::fake();

    $admin = User::factory()->create();
    $students = Student::factory()->count(3)->create();

    actingAs($admin)
        ->post(route('students.bulk-login-links'), [
            'student_ids' => $students->pluck('id')->all(),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSentCount(3);
});

test('instructor cannot send bulk login links', function () {
    $instructor = User::factory()->instructor()->create();
    $student = Student::factory()->create();

    actingAs($instructor)
        ->post(route('students.bulk-login-links'), [
            'student_ids' => [$student->id],
        ])
        ->assertForbidden();
});

test('bulk login links validates student ids', function () {
    $admin = User::factory()->create();

    actingAs($admin)
        ->post(route('students.bulk-login-links'), [
            'student_ids' => [],
        ])
        ->assertSessionHasErrors('student_ids');
});
