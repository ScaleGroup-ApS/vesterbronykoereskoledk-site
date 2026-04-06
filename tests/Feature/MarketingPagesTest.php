<?php

use App\Models\Course;
use App\Models\Offer;
use App\Models\Team;

use function Pest\Laravel\get;

test('guests can view the public home page', function () {
    get(route('home'))
        ->assertOk()
        ->assertViewIs('welcome')
        ->assertViewHas('homeCopy')
        ->assertViewHas('valueBlocks')
        ->assertViewHas('testimonials')
        ->assertViewHas('nextHoldStartAt')
        ->assertViewHas('heroHoldSpotsRemaining')
        ->assertViewHas('tilmeldHoldstartOfferSlug');
});

test('home page passes public spots from featured course when set', function () {
    $offer = Offer::factory()->create();
    $start = now()->addDays(10)->startOfHour();
    Course::factory()->for($offer)->create([
        'start_at' => $start,
        'end_at' => $start->copy()->addHours(8),
        'featured_on_home' => true,
        'public_spots_remaining' => 4,
    ]);

    get(route('home'))
        ->assertOk()
        ->assertViewHas('heroHoldSpotsRemaining', 4)
        ->assertViewHas('nextHoldStartAt', $start->toIso8601String());
});

test('home page exposes next hold start from earliest upcoming course', function () {
    $offer = Offer::factory()->create(['slug' => 'basis-pakke']);
    $start = now()->addDays(10)->startOfHour();
    Course::factory()->for($offer)->create([
        'start_at' => $start,
        'end_at' => $start->copy()->addMonths(3),
    ]);

    get(route('home'))
        ->assertOk()
        ->assertViewIs('welcome')
        ->assertViewHas('nextHoldStartAt', $start->toIso8601String())
        ->assertViewHas('tilmeldHoldstartOfferSlug', 'basis-pakke');
});

test('guests can view marketing subpages', function (string $routeName, string $viewName) {
    get(route($routeName))
        ->assertOk()
        ->assertViewIs($viewName);
})->with([
    ['marketing.features', 'marketing.fordele'],
    ['marketing.about', 'marketing.om-os'],
    ['marketing.contact', 'marketing.kontakt'],
    ['marketing.faq', 'marketing.faq'],
    ['marketing.instructors', 'marketing.vores-korelaerere'],
    ['marketing.terms', 'marketing.handelsbetingelser'],
    ['marketing.privacy', 'marketing.privatlivspolitik'],
    ['marketing.cookies', 'marketing.cookiepolitik'],
]);

test('guests can view the packages page with offers', function () {
    Offer::factory()->create(['name' => 'Basis']);

    get(route('marketing.packages'))
        ->assertOk()
        ->assertViewIs('marketing.pakker')
        ->assertViewHas('offers', fn ($offers) => $offers->count() === 1 && $offers->first()->name === 'Basis')
        ->assertViewHas('addons', fn ($addons) => $addons->count() === 0);
});

test('packages page lists primary offers and addons separately', function () {
    Offer::factory()->create(['name' => 'Lovpakke A']);
    Offer::factory()->addon()->create(['name' => 'Ekstra time']);

    get(route('marketing.packages'))
        ->assertOk()
        ->assertViewIs('marketing.pakker')
        ->assertViewHas('offers', fn ($offers) => $offers->count() === 1 && $offers->first()->name === 'Lovpakke A')
        ->assertViewHas('addons', fn ($addons) => $addons->count() === 1 && $addons->first()->name === 'Ekstra time');
});

test('marketing package detail returns 404 for addon offers', function () {
    $addon = Offer::factory()->addon()->create();

    get(route('marketing.packages.show', $addon))->assertNotFound();
});

test('guests can view a single package page by slug', function () {
    $offer = Offer::factory()->create(['name' => 'Basis Pakke']);

    get(route('marketing.packages.show', $offer))
        ->assertOk()
        ->assertViewIs('marketing.pakke-show')
        ->assertViewHas('offer', fn ($o) => $o->name === 'Basis Pakke' && $o->slug === $offer->slug);
});

test('shared marketing offers only include primary packages', function () {
    Offer::factory()->create(['name' => 'Primær']);
    Offer::factory()->addon()->create(['name' => 'Tilvalg']);

    get(route('home'))
        ->assertOk()
        ->assertViewHas('marketingOffers', fn ($offers) => $offers->count() === 1 && $offers->first()->name === 'Primær');
});

test('home page uses first primary offer when next course is an addon offer', function () {
    $primary = Offer::factory()->create(['slug' => 'primær-hold', 'name' => 'Primær']);
    $addon = Offer::factory()->addon()->create(['slug' => 'addon-kursus']);
    $start = now()->addDays(10)->startOfHour();
    Course::factory()->for($addon)->create([
        'start_at' => $start,
        'end_at' => $start->copy()->addMonths(3),
    ]);

    get(route('home'))
        ->assertOk()
        ->assertViewHas('nextHoldStartAt', $start->toIso8601String())
        ->assertViewHas('tilmeldHoldstartOfferSlug', 'primær-hold');
});

test('guests can view til elever content pages', function () {
    get(route('marketing.for-students.show', ['slug' => 'elevportal']))
        ->assertOk()
        ->assertViewIs('marketing.for-students')
        ->assertViewHas('slug', 'elevportal')
        ->assertViewHas('heading', 'Køreklar sammen – elevportal');
});

test('unknown til elever slugs return 404', function () {
    get(route('marketing.for-students.show', ['slug' => 'findes-ikke']))
        ->assertNotFound();
});

test('guests can view instructors page with teams', function () {
    Team::factory()->create(['name' => 'Team Alpha']);

    get(route('marketing.instructors'))
        ->assertOk()
        ->assertViewIs('marketing.vores-korelaerere')
        ->assertViewHas('teams', fn ($teams) => $teams->count() === 1 && $teams->first()->name === 'Team Alpha');
});
