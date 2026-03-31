<?php

use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can search payments by student name', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();
    $student->user->update(['name' => 'Lars Nielsen']);
    Payment::factory()->create(['student_id' => $student->id]);
    Payment::factory()->create(); // other student

    actingAs($admin)
        ->get(route('payments.index', ['search' => 'Lars']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('payments.data', 1)
            ->has('filters')
            ->where('filters.search', 'Lars')
        );
});

test('admin can filter payments by method', function () {
    $admin = User::factory()->create();
    Payment::factory()->create(['method' => PaymentMethod::Cash]);
    Payment::factory()->create(['method' => PaymentMethod::Card]);

    actingAs($admin)
        ->get(route('payments.index', ['method' => 'cash']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('payments.data', 1)
            ->where('filters.method', 'cash')
        );
});

test('payments index returns filters prop', function () {
    $admin = User::factory()->create();

    actingAs($admin)
        ->get(route('payments.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('filters')
        );
});
