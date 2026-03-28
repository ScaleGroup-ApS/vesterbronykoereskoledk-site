<?php

use App\Models\Course;
use App\Models\Offer;
use App\Models\Team;

use function Pest\Laravel\get;

test('guests can view the public home page', function () {
    get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('marketingContact')
            ->where('marketingContact.phone', config('marketing.contact.phone'))
            ->where('marketingContact.email', config('marketing.contact.email'))
            ->has('homeCopy')
            ->has('valueBlocks')
            ->has('testimonials')
            ->has('nextHoldStartAt')
            ->has('tilmeldHoldstartOfferSlug')
        );
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
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('nextHoldStartAt', $start->toIso8601String())
            ->where('tilmeldHoldstartOfferSlug', 'basis-pakke'));
});

test('guests can view marketing subpages', function (string $routeName, string $component) {
    get(route($routeName))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component($component));
})->with([
    ['marketing.features', 'marketing/fordele'],
    ['marketing.about', 'marketing/om-os'],
    ['marketing.contact', 'marketing/kontakt'],
    ['marketing.faq', 'marketing/faq'],
    ['marketing.instructors', 'marketing/vores-korelaerere'],
    ['marketing.terms', 'marketing/handelsbetingelser'],
    ['marketing.privacy', 'marketing/privatlivspolitik'],
    ['marketing.cookies', 'marketing/cookiepolitik'],
]);

test('guests can view the packages page with offers', function () {
    Offer::factory()->create(['name' => 'Basis']);

    get(route('marketing.packages'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/pakker')
            ->has('offers', 1)
            ->where('offers.0.name', 'Basis')
        );
});

test('guests can view a single package page by slug', function () {
    $offer = Offer::factory()->create(['name' => 'Basis Pakke']);

    get(route('marketing.packages.show', $offer))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/pakke-show')
            ->where('offer.name', 'Basis Pakke')
            ->where('offer.slug', $offer->slug)
        );
});

test('guests can view til elever content pages', function () {
    get(route('marketing.til-elever.show', ['slug' => 'elevportal']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/til-elever-side')
            ->where('slug', 'elevportal')
            ->where('heading', 'Køreklar sammen – elevportal')
        );
});

test('unknown til elever slugs return 404', function () {
    get(route('marketing.til-elever.show', ['slug' => 'findes-ikke']))
        ->assertNotFound();
});

test('guests can view instructors page with teams', function () {
    Team::factory()->create(['name' => 'Team Alpha']);

    get(route('marketing.instructors'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/vores-korelaerere')
            ->has('teams', 1)
            ->where('teams.0.name', 'Team Alpha')
        );
});
