<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

$fakeTickets = [
    [
        'id' => 1,
        'subject' => 'Login issue',
        'status' => 'open',
        'priority' => 'high',
        'createdAt' => '2026-04-01T10:00:00.000Z',
        'origin' => 'driving-school',
    ],
    [
        'id' => 2,
        'subject' => 'Invoice question',
        'status' => 'solved',
        'priority' => 'normal',
        'createdAt' => '2026-03-28T09:00:00.000Z',
        'origin' => 'driving-school',
    ],
];

$fakeTicket = [
    'id' => 1,
    'subject' => 'Login issue',
    'status' => 'open',
    'priority' => 'high',
    'createdAt' => '2026-04-01T10:00:00.000Z',
    'origin' => 'driving-school',
    'threads' => [
        [
            'id' => 1,
            'comments' => [
                [
                    'id' => 1,
                    'content' => 'Cannot log in.',
                    'authorType' => 'customer',
                    'createdAt' => '2026-04-01T10:00:00.000Z',
                    'author' => null,
                ],
            ],
        ],
    ],
];

test('unauthenticated user is redirected from support index', function () {
    $this->get(route('support.index'))
        ->assertRedirect(route('login'));
});

test('student cannot access support index', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get(route('support.index'))
        ->assertForbidden();
});

test('admin can create a ticket with valid data', function () {
    Http::fake(['*/api/driving-schools/tickets*' => Http::response([])]);

    $this->actingAs(User::factory()->create())
        ->post(route('support.store'), [
            'subject' => 'Login issue',
            'message' => 'Cannot log in.',
            'priority' => 'high',
        ])
        ->assertRedirect(route('support.index'));
});

test('ticket creation fails with missing subject', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('support.store'), [
            'subject' => '',
            'message' => 'Some message.',
            'priority' => 'normal',
        ])
        ->assertSessionHasErrors('subject');
});

test('ticket creation fails with missing message', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('support.store'), [
            'subject' => 'A subject',
            'message' => '',
            'priority' => 'normal',
        ])
        ->assertSessionHasErrors('message');
});

test('ticket creation fails with invalid priority', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('support.store'), [
            'subject' => 'A subject',
            'message' => 'Some message.',
            'priority' => 'super-urgent',
        ])
        ->assertSessionHasErrors('priority');
});

test('admin can add a comment to a ticket', function () {
    Http::fake(['*/api/driving-schools/tickets*' => Http::response([])]);

    $this->actingAs(User::factory()->create())
        ->post(route('support.comment', 1), [
            'message' => 'Looking into it.',
        ])
        ->assertRedirect(route('support.show', 1));
});

test('comment requires a message', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('support.comment', 1), [
            'message' => '',
        ])
        ->assertSessionHasErrors('message');
});
