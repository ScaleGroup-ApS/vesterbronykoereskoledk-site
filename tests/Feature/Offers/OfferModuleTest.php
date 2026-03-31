<?php

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\Student;
use App\Models\User;

test('admin can view modules index', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    OfferModule::factory()->for($offer)->count(2)->create();

    $this->actingAs($admin)
        ->get(route('offers.modules.index', $offer))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('offers/modules/index')
            ->has('modules', 2)
        );
});

test('instructor can view modules index', function () {
    $instructor = User::factory()->instructor()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($instructor)
        ->get(route('offers.modules.index', $offer))
        ->assertOk();
});

test('student cannot view modules index', function () {
    $student = Student::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($student->user)
        ->get(route('offers.modules.index', $offer))
        ->assertForbidden();
});

test('admin can create a module', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->post(route('offers.modules.store', $offer), ['title' => 'Modul 1'])
        ->assertRedirect(route('offers.modules.index', $offer));

    expect(OfferModule::where('offer_id', $offer->id)->count())->toBe(1);
    expect(OfferModule::first()->title)->toBe('Modul 1');
});

test('instructor can create a module', function () {
    $instructor = User::factory()->instructor()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($instructor)
        ->post(route('offers.modules.store', $offer), ['title' => 'Modul A'])
        ->assertRedirect(route('offers.modules.index', $offer));

    expect(OfferModule::count())->toBe(1);
});

test('admin can update a module', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create(['title' => 'Gammel titel']);

    $this->actingAs($admin)
        ->patch(route('offers.modules.update', [$offer, $module]), ['title' => 'Ny titel'])
        ->assertRedirect(route('offers.modules.index', $offer));

    expect($module->fresh()->title)->toBe('Ny titel');
});

test('admin can delete a module', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();

    $this->actingAs($admin)
        ->delete(route('offers.modules.destroy', [$offer, $module]))
        ->assertRedirect(route('offers.modules.index', $offer));

    expect(OfferModule::find($module->id))->toBeNull();
});

test('admin can move module up', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $first = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $second = OfferModule::factory()->for($offer)->create(['sort_order' => 1]);

    $this->actingAs($admin)
        ->post(route('offers.modules.move-up', [$offer, $second]))
        ->assertRedirect(route('offers.modules.index', $offer));

    expect($second->fresh()->sort_order)->toBe(0);
    expect($first->fresh()->sort_order)->toBe(1);
});

test('admin can move module down', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $first = OfferModule::factory()->for($offer)->create(['sort_order' => 0]);
    $second = OfferModule::factory()->for($offer)->create(['sort_order' => 1]);

    $this->actingAs($admin)
        ->post(route('offers.modules.move-down', [$offer, $first]))
        ->assertRedirect(route('offers.modules.index', $offer));

    expect($first->fresh()->sort_order)->toBe(1);
    expect($second->fresh()->sort_order)->toBe(0);
});

test('student cannot manage modules', function () {
    $student = Student::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($student->user)
        ->post(route('offers.modules.store', $offer), ['title' => 'Ulovligt modul'])
        ->assertForbidden();
});
