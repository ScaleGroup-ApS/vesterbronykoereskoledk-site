<?php

use App\Models\User;
use App\Services\CrmTicketService;

$fakeTickets = [
    [
        'id' => 1,
        'subject' => 'Login virker ikke',
        'status' => 'open',
        'priority' => 'high',
        'createdAt' => '2026-04-01T10:00:00.000Z',
        'origin' => 'Køreskole',
    ],
    [
        'id' => 2,
        'subject' => 'Faktura spørgsmål',
        'status' => 'solved',
        'priority' => 'normal',
        'createdAt' => '2026-03-28T09:00:00.000Z',
        'origin' => 'Køreskole',
    ],
];

$fakeTicket = [
    'id' => 1,
    'subject' => 'Login virker ikke',
    'status' => 'open',
    'priority' => 'high',
    'createdAt' => '2026-04-01T10:00:00.000Z',
    'origin' => 'Køreskole',
    'threads' => [
        [
            'id' => 1,
            'comments' => [
                [
                    'id' => 1,
                    'content' => 'Jeg kan ikke logge ind.',
                    'authorType' => 'customer',
                    'createdAt' => '2026-04-01T10:00:00.000Z',
                    'author' => null,
                ],
            ],
        ],
    ],
];

test('admin can view support index', function () use ($fakeTickets) {
    $admin = User::factory()->create();

    $this->mock(CrmTicketService::class)
        ->shouldReceive('getTickets')
        ->once()
        ->andReturn($fakeTickets);

    $this->actingAs($admin)
        ->get(route('support.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Support/Index')
            ->has('tickets', 2)
        );
});

test('instructor can view support index', function () use ($fakeTickets) {
    $instructor = User::factory()->instructor()->create();

    $this->mock(CrmTicketService::class)
        ->shouldReceive('getTickets')
        ->once()
        ->andReturn($fakeTickets);

    $this->actingAs($instructor)
        ->get(route('support.index'))
        ->assertOk();
});

test('unauthenticated user is redirected from support index', function () {
    $this->get(route('support.index'))
        ->assertRedirect(route('login'));
});

test('student cannot access support index', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('support.index'))
        ->assertForbidden();
});

test('admin can create a ticket with valid data', function () {
    $admin = User::factory()->create();

    $this->mock(CrmTicketService::class)
        ->shouldReceive('createTicket')
        ->once()
        ->with('Login virker ikke', 'Jeg kan ikke logge ind.', 'high')
        ->andReturn([]);

    $this->actingAs($admin)
        ->post(route('support.store'), [
            'subject' => 'Login virker ikke',
            'message' => 'Jeg kan ikke logge ind.',
            'priority' => 'high',
        ])
        ->assertRedirect(route('support.index'));
});

test('ticket creation fails with missing subject', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('support.store'), [
            'subject' => '',
            'message' => 'Noget besked.',
            'priority' => 'normal',
        ])
        ->assertSessionHasErrors('subject');
});

test('ticket creation fails with missing message', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('support.store'), [
            'subject' => 'Et emne',
            'message' => '',
            'priority' => 'normal',
        ])
        ->assertSessionHasErrors('message');
});

test('ticket creation fails with invalid priority', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('support.store'), [
            'subject' => 'Et emne',
            'message' => 'Noget besked.',
            'priority' => 'super-urgent',
        ])
        ->assertSessionHasErrors('priority');
});

test('admin can view ticket detail', function () use ($fakeTicket) {
    $admin = User::factory()->create();

    $this->mock(CrmTicketService::class)
        ->shouldReceive('getTicket')
        ->once()
        ->with(1)
        ->andReturn($fakeTicket);

    $this->actingAs($admin)
        ->get(route('support.show', 1))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Support/Show')
            ->has('ticket')
        );
});

test('admin can add a comment to a ticket', function () {
    $admin = User::factory()->create();

    $this->mock(CrmTicketService::class)
        ->shouldReceive('addComment')
        ->once()
        ->with(1, 'Vi kigger på det.')
        ->andReturn([]);

    $this->actingAs($admin)
        ->post(route('support.comment', 1), [
            'message' => 'Vi kigger på det.',
        ])
        ->assertRedirect(route('support.show', 1));
});

test('comment requires a message', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('support.comment', 1), [
            'message' => '',
        ])
        ->assertSessionHasErrors('message');
});
