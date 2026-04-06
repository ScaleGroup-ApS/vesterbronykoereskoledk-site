<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('participant can send a message with an attachment', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)
        ->post(route('chat.messages.store', $conversation), [
            'body' => 'Tjek denne fil',
            'attachments' => [$file],
        ]);

    $response->assertCreated();

    $message = Message::first();
    expect($message->getMedia('attachments'))->toHaveCount(1);
});

test('participant can send attachment without body text', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)
        ->post(route('chat.messages.store', $conversation), [
            'attachments' => [$file],
        ]);

    $response->assertCreated();

    $message = Message::first();
    expect($message->body)->toBeNull();
    expect($message->getMedia('attachments'))->toHaveCount(1);
});

test('message store requires body or attachments', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $this->actingAs($user)
        ->postJson(route('chat.messages.store', $conversation), [])
        ->assertUnprocessable();
});

test('attachment response includes attachment metadata', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $file = UploadedFile::fake()->create('report.pdf', 50, 'application/pdf');

    $response = $this->actingAs($user)
        ->postJson(route('chat.messages.store', $conversation), [
            'body' => 'Rapport vedlagt',
            'attachments' => [$file],
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['attachments' => [['id', 'name', 'mime_type', 'size', 'url']]]);
});

test('messages index includes attachments', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $message = Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'body' => 'Med fil',
    ]);

    $file = UploadedFile::fake()->create('notes.txt', 1, 'text/plain');
    $message->addMedia($file)->toMediaCollection('attachments');

    $this->actingAs($user)
        ->getJson(route('chat.messages.index', $conversation))
        ->assertJsonPath('data.0.attachments.0.name', 'notes.txt');
});

test('participant can download attachment via named route', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $message = Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
    ]);

    $file = UploadedFile::fake()->create('contract.pdf', 10, 'application/pdf');
    $media = $message->addMedia($file)->toMediaCollection('attachments');

    $this->actingAs($user)
        ->get(route('chat.messages.attachments.show', [
            'conversation' => $conversation->id,
            'message' => $message->id,
            'media' => $media->id,
        ]))
        ->assertSuccessful();
});

test('non-participant cannot download attachment', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($owner->id);

    $message = Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $owner->id,
    ]);

    $file = UploadedFile::fake()->create('secret.pdf', 5, 'application/pdf');
    $media = $message->addMedia($file)->toMediaCollection('attachments');

    $this->actingAs($outsider)
        ->get(route('chat.messages.attachments.show', [
            'conversation' => $conversation->id,
            'message' => $message->id,
            'media' => $media->id,
        ]))
        ->assertForbidden();
});

test('attachment limit of 10 files is enforced', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $conversation->users()->attach($user->id);

    $files = array_fill(0, 11, UploadedFile::fake()->create('file.pdf', 1, 'application/pdf'));

    $this->actingAs($user)
        ->postJson(route('chat.messages.store', $conversation), [
            'attachments' => $files,
        ])
        ->assertUnprocessable();
});
