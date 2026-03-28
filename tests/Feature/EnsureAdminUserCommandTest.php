<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('admin ensure user command creates admin with known password', function () {
    $this->artisan('admin:ensure-user', [
        '--email' => 'admin-e2e@test.dk',
        '--password' => 'secret123',
    ])->assertSuccessful();

    $user = User::query()->where('email', 'admin-e2e@test.dk')->first();

    expect($user)->not->toBeNull();
    expect($user->role->value)->toBe('admin');
    expect(Hash::check('secret123', $user->password))->toBeTrue();
});
