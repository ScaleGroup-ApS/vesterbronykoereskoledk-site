<?php

use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can upload image to offer materials', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->post(route('offers.media.store', $offer), [
            'file' => UploadedFile::fake()->image('photo.jpg'),
        ])
        ->assertRedirect();

    expect($offer->getMedia('materials'))->toHaveCount(1);
});

test('admin can upload video to offer materials', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->post(route('offers.media.store', $offer), [
            'file' => UploadedFile::fake()->create('lesson.mp4', 1024, 'video/mp4'),
        ])
        ->assertRedirect();

    expect($offer->getMedia('materials'))->toHaveCount(1);
});

test('non-admin cannot upload media to offer', function () {
    $user = User::factory()->create(['role' => 'instructor']);
    $offer = Offer::factory()->create();

    $this->actingAs($user)
        ->post(route('offers.media.store', $offer), [
            'file' => UploadedFile::fake()->image('photo.jpg'),
        ])
        ->assertForbidden();
});

test('admin can delete offer media', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $media = $offer->addMedia(UploadedFile::fake()->image('photo.jpg'))
        ->toMediaCollection('materials');

    $this->actingAs($admin)
        ->delete(route('offers.media.destroy', [$offer, $media]))
        ->assertRedirect();

    expect($offer->fresh()->getMedia('materials'))->toHaveCount(0);
});

test('admin can view offer media', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $media = $offer->addMedia(UploadedFile::fake()->image('photo.jpg'))
        ->toMediaCollection('materials');

    $this->actingAs($admin)
        ->get(route('offers.media.show', [$offer, $media]))
        ->assertSuccessful();
});

test('cannot access media belonging to a different offer', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer1 = Offer::factory()->create();
    $offer2 = Offer::factory()->create();

    $media = $offer2->addMedia(UploadedFile::fake()->image('photo.jpg'))
        ->toMediaCollection('materials');

    $this->actingAs($admin)
        ->get(route('offers.media.show', [$offer1, $media]))
        ->assertNotFound();
});

test('invalid file type is rejected', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->post(route('offers.media.store', $offer), [
            'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])
        ->assertInvalid(['file']);
});

test('offer edit page includes materials', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $offer->addMedia(UploadedFile::fake()->image('photo.jpg'))
        ->toMediaCollection('materials');

    $this->actingAs($admin)
        ->get(route('offers.edit', $offer))
        ->assertInertia(fn ($page) => $page
            ->component('offers/edit')
            ->has('materials', 1)
        );
});
