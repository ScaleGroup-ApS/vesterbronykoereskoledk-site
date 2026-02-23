<?php

use App\Models\Course;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('admin receives courseEvents and offers props', function () {
    $admin = User::factory()->create();
    Course::factory()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('courseEvents', 1)
            ->has('offers')
        );
});

test('instructor receives courseEvents and offers props', function () {
    $instructor = User::factory()->instructor()->create();
    Course::factory()->create();

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('courseEvents', 1)
            ->has('offers')
        );
});

test('student does not receive courseEvents or offers', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->where('courseEvents', [])
            ->where('offers', [])
        );
});
