<?php

use App\Models\Student;
use App\Models\User;

it('student can view payments page', function () {
    $user = User::factory()->create(['role' => 'student']);
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.payments'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/payments')
            ->has('balance')
            ->has('payments')
            ->has('offer_prices')
        );
});

it('non-student cannot access payments page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get(route('student.payments'))
        ->assertStatus(403);
});

it('guest cannot access payments page', function () {
    $this->get(route('student.payments'))
        ->assertRedirect(route('login'));
});
