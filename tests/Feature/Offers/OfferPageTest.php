<?php

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can create a page', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.store', [$offer, $module]), [
            'title' => 'Introduktion',
            'body' => '<p>Velkomst</p>',
            'video_url' => null,
        ])
        ->assertRedirect(route('offers.modules.index', $offer));

    expect(OfferPage::count())->toBe(1);
    expect(OfferPage::first()->title)->toBe('Introduktion');
    expect(OfferPage::first()->body)->toBe('<p>Velkomst</p>');
});

test('admin can create a page with file attachment', function () {
    Storage::fake('media');
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.store', [$offer, $module]), [
            'title' => 'Med fil',
            'attachment' => UploadedFile::fake()->create('guide.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    $page = OfferPage::first();
    expect($page->getMedia('attachments')->count())->toBe(1);
});

test('admin can edit a page', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $this->actingAs($admin)
        ->get(route('offers.modules.pages.edit', [$offer, $module, $page]))
        ->assertOk()
        ->assertInertia(fn ($p) => $p->component('offers/pages/edit'));
});

test('admin can update a page', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create(['title' => 'Gammel titel']);

    $this->actingAs($admin)
        ->patch(route('offers.modules.pages.update', [$offer, $module, $page]), [
            'title' => 'Ny titel',
        ])
        ->assertRedirect(route('offers.modules.index', $offer));

    expect($page->fresh()->title)->toBe('Ny titel');
});

test('admin can delete a page', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $this->actingAs($admin)
        ->delete(route('offers.modules.pages.destroy', [$offer, $module, $page]))
        ->assertRedirect(route('offers.modules.index', $offer));

    expect(OfferPage::find($page->id))->toBeNull();
});

test('admin can reorder pages within a module', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $first = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 0]);
    $second = OfferPage::factory()->for($module, 'module')->create(['sort_order' => 1]);

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.move-up', [$offer, $module, $second]))
        ->assertRedirect();

    expect($second->fresh()->sort_order)->toBe(0);
    expect($first->fresh()->sort_order)->toBe(1);
});
