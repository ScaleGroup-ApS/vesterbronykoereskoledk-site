<?php

use App\Actions\Student\CreateStudent;
use App\Actions\Student\SendStudentLoginLink;
use App\Mail\StudentMagicLoginMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\post;

it('sends a magic link when a valid student email is provided', function () {
    Mail::fake();

    $student = app(CreateStudent::class)->handle([
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'phone' => '12345678',
        'cpr' => '010100-1234',
        'start_date' => now()->toDateString(),
    ], app(SendStudentLoginLink::class));

    Mail::fake();

    post(route('login.magic-link'), [
        'email' => 'student@example.com',
    ])
        ->assertRedirect()
        ->assertSessionHas('status');

    Mail::assertSent(StudentMagicLoginMail::class, function ($mail) {
        return $mail->hasTo('student@example.com');
    });
});

it('returns success even for non-existent emails to prevent enumeration', function () {
    Mail::fake();

    post(route('login.magic-link'), [
        'email' => 'nonexistent@example.com',
    ])
        ->assertRedirect()
        ->assertSessionHas('status');

    Mail::assertNothingSent();
});

it('returns success for non-student user emails without sending a link', function () {
    Mail::fake();

    User::factory()->create([
        'email' => 'admin@example.com',
        'role' => 'admin',
    ]);

    post(route('login.magic-link'), [
        'email' => 'admin@example.com',
    ])
        ->assertRedirect()
        ->assertSessionHas('status');

    Mail::assertNothingSent();
});

it('validates that email is required', function () {
    post(route('login.magic-link'), [
        'email' => '',
    ])->assertSessionHasErrors('email');
});

it('validates that email is a valid email address', function () {
    post(route('login.magic-link'), [
        'email' => 'not-an-email',
    ])->assertSessionHasErrors('email');
});

it('is rate limited to prevent abuse', function () {
    Mail::fake();

    for ($i = 0; $i < 5; $i++) {
        post(route('login.magic-link'), [
            'email' => "test{$i}@example.com",
        ])->assertRedirect();
    }

    post(route('login.magic-link'), [
        'email' => 'extra@example.com',
    ])->assertStatus(429);
});
