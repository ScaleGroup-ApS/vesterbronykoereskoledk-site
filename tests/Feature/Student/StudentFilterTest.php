<?php

use App\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can search students by name', function () {
    $admin = User::factory()->create();
    $match = Student::factory()->create();
    $match->user->update(['name' => 'Anders Jensen']);
    Student::factory()->create();

    actingAs($admin)
        ->get(route('students.index', ['search' => 'Anders']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('students.data', 1)
            ->where('students.data.0.user.name', 'Anders Jensen')
            ->has('filters')
            ->where('filters.search', 'Anders')
        );
});

test('admin can filter students by status', function () {
    $admin = User::factory()->create();
    Student::factory()->count(2)->create(); // active
    Student::factory()->inactive()->create();

    actingAs($admin)
        ->get(route('students.index', ['status' => 'inactive']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('students.data', 1)
            ->where('filters.status', 'inactive')
        );
});

test('admin can sort students by name ascending', function () {
    $admin = User::factory()->create();
    $studentA = Student::factory()->create();
    $studentA->user->update(['name' => 'Aaa']);
    $studentB = Student::factory()->create();
    $studentB->user->update(['name' => 'Zzz']);

    actingAs($admin)
        ->get(route('students.index', ['sort' => 'name', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('students.data.0.user.name', 'Aaa')
            ->where('filters.sort', 'name')
            ->where('filters.direction', 'asc')
        );
});

test('invalid sort field defaults to created_at', function () {
    $admin = User::factory()->create();
    Student::factory()->create();

    actingAs($admin)
        ->get(route('students.index', ['sort' => 'invalid_field']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('filters.sort', 'created_at')
        );
});

test('search and filter work together', function () {
    $admin = User::factory()->create();

    $activeMatch = Student::factory()->create();
    $activeMatch->user->update(['name' => 'Erik Active']);

    $inactiveMatch = Student::factory()->inactive()->create();
    $inactiveMatch->user->update(['name' => 'Erik Inactive']);

    actingAs($admin)
        ->get(route('students.index', ['search' => 'Erik', 'status' => 'active']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('students.data', 1)
            ->where('students.data.0.user.name', 'Erik Active')
        );
});
