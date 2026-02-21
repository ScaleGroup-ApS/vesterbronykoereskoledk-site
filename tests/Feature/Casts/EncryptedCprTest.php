<?php

use App\Casts\EncryptedCpr;
use Illuminate\Database\Eloquent\Model;

test('encrypts value when setting', function () {
    $cast = new EncryptedCpr;
    $model = Mockery::mock(Model::class);

    $encrypted = $cast->set($model, 'cpr', '010190-1234', []);

    expect($encrypted)->not->toBe('010190-1234');
    expect($encrypted)->toBeString();
});

test('decrypts value when getting', function () {
    $cast = new EncryptedCpr;
    $model = Mockery::mock(Model::class);

    $encrypted = $cast->set($model, 'cpr', '010190-1234', []);
    $decrypted = $cast->get($model, 'cpr', $encrypted, []);

    expect($decrypted)->toBe('010190-1234');
});

test('handles null values', function () {
    $cast = new EncryptedCpr;
    $model = Mockery::mock(Model::class);

    expect($cast->get($model, 'cpr', null, []))->toBeNull();
    expect($cast->set($model, 'cpr', null, []))->toBeNull();
});
