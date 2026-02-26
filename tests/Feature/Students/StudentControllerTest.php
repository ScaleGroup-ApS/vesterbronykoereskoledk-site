<?php

use App\Models\Student;
use App\Models\User;

test('admin can view students index', function () {
    $admin = User::factory()->create();
    Student::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('students.index'))
        ->assertOk();
});

test('instructor can view students index', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get(route('students.index'))
        ->assertOk();
});

test('student cannot view students index', function () {
    $student = Student::factory()->create();

    $this->actingAs($student->user)
        ->get(route('students.index'))
        ->assertForbidden();
});

test('admin can create a student', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('students.store'), [
            'name' => 'Jonas Hansen',
            'email' => 'jonas@example.com',
            'phone' => '+4512345678',
            'cpr' => '010190-1234',
            'start_date' => '2026-03-01',
        ])
        ->assertRedirect();

    expect(Student::count())->toBe(1);
});

test('admin can update a student', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();

    $this->actingAs($admin)
        ->put(route('students.update', $student), [
            'name' => 'Updated Name',
            'email' => $student->user->email,
        ])
        ->assertRedirect();

    expect($student->fresh()->user->name)->toBe('Updated Name');
});

test('admin can delete a student', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();

    $this->actingAs($admin)
        ->delete(route('students.destroy', $student))
        ->assertRedirect(route('students.index'));

    expect(Student::find($student->id))->toBeNull();
});

test('instructor cannot create a student', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->post(route('students.store'), [
            'name' => 'Jonas Hansen',
            'email' => 'jonas@example.com',
        ])
        ->assertForbidden();
});

test('student can view own profile', function () {
    $student = Student::factory()->create();

    $this->actingAs($student->user)
        ->get(route('students.show', $student))
        ->assertOk();
});

test('student cannot view other student profile', function () {
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();

    $this->actingAs($student1->user)
        ->get(route('students.show', $student2))
        ->assertForbidden();
});
