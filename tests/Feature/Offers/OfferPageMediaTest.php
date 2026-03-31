<?php

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function pageWithModule(): array
{
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    return [$offer, $module, $page];
}

// ── Attachments ──────────────────────────────────────────────────────────────

test('admin can upload attachment to page', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.media.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    expect($page->getMedia('attachments'))->toHaveCount(1);
});

test('instructor can upload attachment to page', function () {
    Storage::fake('media');
    $instructor = User::factory()->create(['role' => 'instructor']);
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($instructor)
        ->post(route('offers.modules.pages.media.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('notes.docx', 200, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ])
        ->assertRedirect();

    expect($page->getMedia('attachments'))->toHaveCount(1);
});

test('student cannot upload attachment to page', function () {
    $student = User::factory()->create(['role' => 'student']);
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($student)
        ->post(route('offers.modules.pages.media.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])
        ->assertForbidden();
});

test('attachment rejects invalid file type', function () {
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.media.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        ])
        ->assertInvalid(['file']);
});

test('admin can view page attachment', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $media = $page->addMedia(UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'))
        ->toMediaCollection('attachments');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.media.show', [$offer, $module, $page, $media]))
        ->assertSuccessful();
});

test('cannot view attachment belonging to a different page', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page1] = pageWithModule();
    $page2 = OfferPage::factory()->for($module, 'module')->create();

    $media = $page2->addMedia(UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'))
        ->toMediaCollection('attachments');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.media.show', [$offer, $module, $page1, $media]))
        ->assertNotFound();
});

test('admin can delete page attachment', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $media = $page->addMedia(UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'))
        ->toMediaCollection('attachments');

    $this->actingAs($admin)
        ->delete(route('offers.modules.pages.media.destroy', [$offer, $module, $page, $media]))
        ->assertRedirect();

    expect($page->fresh()->getMedia('attachments'))->toHaveCount(0);
});

test('multiple attachments can be uploaded independently', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.media.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('doc1.pdf', 100, 'application/pdf'),
        ]);

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.media.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('doc2.pdf', 100, 'application/pdf'),
        ]);

    expect($page->getMedia('attachments'))->toHaveCount(2);
});

// ── Banner ────────────────────────────────────────────────────────────────────

test('admin can upload banner image', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.banner.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('banner.jpg', 500, 'image/jpeg'),
        ])
        ->assertRedirect();

    expect($page->getFirstMedia('banner'))->not->toBeNull();
});

test('uploading a new banner replaces the existing one', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.banner.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('first.jpg', 300, 'image/jpeg'),
        ]);

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.banner.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('second.jpg', 400, 'image/jpeg'),
        ]);

    expect($page->getMedia('banner'))->toHaveCount(1)
        ->and($page->getFirstMedia('banner')->file_name)->toBe('second.jpg');
});

test('banner rejects non-image files', function () {
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.banner.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('video.mp4', 100, 'video/mp4'),
        ])
        ->assertInvalid(['file']);
});

test('admin can view banner', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('banner.jpg', 300, 'image/jpeg'))
        ->toMediaCollection('banner');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.banner.show', [$offer, $module, $page]))
        ->assertSuccessful();
});

test('viewing banner returns 404 when none uploaded', function () {
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.banner.show', [$offer, $module, $page]))
        ->assertNotFound();
});

test('admin can delete banner', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('banner.jpg', 300, 'image/jpeg'))
        ->toMediaCollection('banner');

    $this->actingAs($admin)
        ->delete(route('offers.modules.pages.banner.destroy', [$offer, $module, $page]))
        ->assertRedirect();

    expect($page->fresh()->getFirstMedia('banner'))->toBeNull();
});

// ── Video ─────────────────────────────────────────────────────────────────────

test('admin can upload video', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.video.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('lesson.mp4', 1024, 'video/mp4'),
        ])
        ->assertRedirect();

    expect($page->getFirstMedia('video'))->not->toBeNull();
});

test('uploading a new video replaces the existing one', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.video.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('first.mp4', 512, 'video/mp4'),
        ]);

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.video.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('second.mp4', 1024, 'video/mp4'),
        ]);

    expect($page->getMedia('video'))->toHaveCount(1)
        ->and($page->getFirstMedia('video')->file_name)->toBe('second.mp4');
});

test('video rejects non-video files', function () {
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.video.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])
        ->assertInvalid(['file']);
});

test('admin can view video', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('lesson.mp4', 512, 'video/mp4'))
        ->toMediaCollection('video');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.video.show', [$offer, $module, $page]))
        ->assertSuccessful();
});

test('viewing video returns 404 when none uploaded', function () {
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.video.show', [$offer, $module, $page]))
        ->assertNotFound();
});

test('admin can delete video', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('lesson.mp4', 512, 'video/mp4'))
        ->toMediaCollection('video');

    $this->actingAs($admin)
        ->delete(route('offers.modules.pages.video.destroy', [$offer, $module, $page]))
        ->assertRedirect();

    expect($page->fresh()->getFirstMedia('video'))->toBeNull();
});

test('student cannot upload video', function () {
    $student = User::factory()->create(['role' => 'student']);
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($student)
        ->post(route('offers.modules.pages.video.store', [$offer, $module, $page]), [
            'file' => UploadedFile::fake()->create('lesson.mp4', 512, 'video/mp4'),
        ])
        ->assertForbidden();
});

// ── Edit page props ───────────────────────────────────────────────────────────

test('page edit includes attachments prop', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'))
        ->toMediaCollection('attachments');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.edit', [$offer, $module, $page]))
        ->assertInertia(fn ($assert) => $assert
            ->component('offers/pages/edit')
            ->where('page.attachments.0.file_name', 'document.pdf')
        );
});

test('page edit includes banner prop when set', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('banner.jpg', 300, 'image/jpeg'))
        ->toMediaCollection('banner');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.edit', [$offer, $module, $page]))
        ->assertInertia(fn ($assert) => $assert
            ->component('offers/pages/edit')
            ->where('banner.file_name', 'banner.jpg')
            ->whereNull('video')
        );
});

test('page edit includes video prop with processing state', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $page->addMedia(UploadedFile::fake()->create('lesson.mp4', 512, 'video/mp4'))
        ->toMediaCollection('video');

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.edit', [$offer, $module, $page]))
        ->assertInertia(fn ($assert) => $assert
            ->component('offers/pages/edit')
            ->where('video.file_name', 'lesson.mp4')
            ->where('video.processing', true)
            ->whereNull('video.thumbnail_url')
            ->whereNull('banner')
        );
});

test('page update no longer handles attachment field', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    [$offer, $module, $page] = pageWithModule();

    $this->actingAs($admin)
        ->patch(route('offers.modules.pages.update', [$offer, $module, $page]), [
            'title' => 'Updated title',
            'body' => null,
            'video_url' => null,
        ])
        ->assertRedirect();

    expect($page->fresh()->title)->toBe('Updated title')
        ->and($page->fresh()->getMedia('attachments'))->toHaveCount(0);
});
