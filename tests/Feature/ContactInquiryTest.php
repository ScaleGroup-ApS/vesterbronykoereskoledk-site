<?php

use App\Mail\ContactInquiryMail;
use App\Models\Offer;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('guests can view contact page with offers and hold start options', function () {
    Offer::factory()->create(['name' => 'Basis Pakke']);

    get(route('marketing.contact'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('marketing/kontakt')
            ->has('offers', 1)
            ->where('offers.0.name', 'Basis Pakke')
            ->has('holdStartOptions'));
});

test('guests can submit contact form with valid data', function () {
    Mail::fake();

    $offer = Offer::factory()->create();

    post(route('marketing.contact.store'), [
        'name' => 'Test Elev',
        'email' => 'elev@example.com',
        'phone' => '12345678',
        'message' => 'Hej med jer',
        'offer_id' => $offer->id,
        'preferred_hold_start' => 'asap',
    ])
        ->assertRedirect(route('marketing.contact'))
        ->assertSessionHas('success');

    assertDatabaseHas('contact_inquiries', [
        'name' => 'Test Elev',
        'email' => 'elev@example.com',
        'phone' => '12345678',
        'message' => 'Hej med jer',
        'offer_id' => $offer->id,
        'preferred_hold_start' => 'asap',
    ]);

    Mail::assertSent(ContactInquiryMail::class);
});

test('contact form validation requires name', function () {
    post(route('marketing.contact.store'), [
        'email' => 'elev@example.com',
    ])
        ->assertSessionHasErrors('name');
});

test('contact form rejects invalid offer id', function () {
    post(route('marketing.contact.store'), [
        'name' => 'Test',
        'email' => 'elev@example.com',
        'offer_id' => 999999,
    ])
        ->assertSessionHasErrors('offer_id');
});
