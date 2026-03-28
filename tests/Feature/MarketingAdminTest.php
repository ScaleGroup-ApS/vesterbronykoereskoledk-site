<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can view marketing home copy editor', function () {
    $admin = User::factory()->create();

    actingAs($admin)
        ->get(route('marketing.home-copy.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/admin/home-copy')
            ->has('copy')
        );
});

test('admin can view marketing value blocks index', function () {
    $admin = User::factory()->create();

    actingAs($admin)
        ->get(route('marketing.value-blocks.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/admin/value-blocks/index')
            ->has('blocks')
        );
});

test('admin can view marketing testimonials index', function () {
    $admin = User::factory()->create();

    actingAs($admin)
        ->get(route('marketing.testimonials.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/admin/testimonials/index')
            ->has('testimonials')
        );
});

test('instructors cannot access marketing admin routes', function (string $routeName) {
    $instructor = User::factory()->instructor()->create();

    actingAs($instructor)
        ->get(route($routeName))
        ->assertForbidden();
})->with([
    'marketing.home-copy.edit',
    'marketing.value-blocks.index',
    'marketing.testimonials.index',
]);
