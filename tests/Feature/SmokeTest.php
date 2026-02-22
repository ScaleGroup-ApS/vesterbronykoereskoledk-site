<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\be;
use function Pest\Laravel\seed;

test('guest pages', function () {
    visit(['/', '/login'])->assertNoSmoke();
});

test('authenticated pages', function () {
    seed(DatabaseSeeder::class);

    $user = User::first();
    be($user);

    visit([
        '/dashboard',
        '/blog',
        '/students',
        '/teams',
        '/vehicles',
        '/offers',
        '/bookings',
        '/payments',
        '/chat'
    ])->assertNoSmoke();
});