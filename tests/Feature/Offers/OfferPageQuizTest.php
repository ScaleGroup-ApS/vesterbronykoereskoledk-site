<?php

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\OfferPageQuizQuestion;
use App\Models\User;

test('admin can add a quiz question', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.questions.store', [$offer, $module, $page]), [
            'question' => 'Hvad er 2 + 2?',
            'options' => ['3', '4', '5'],
            'correct_option' => 1,
            'explanation' => 'Grundlæggende matematik.',
        ])
        ->assertRedirect();

    expect(OfferPageQuizQuestion::count())->toBe(1);
    expect(OfferPageQuizQuestion::first()->correct_option)->toBe(1);
    expect(OfferPageQuizQuestion::first()->options)->toBe(['3', '4', '5']);
});

test('admin can update a quiz question', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();
    $question = OfferPageQuizQuestion::factory()->for($page, 'page')->create(['question' => 'Gammel?']);

    $this->actingAs($admin)
        ->patch(route('offers.modules.pages.questions.update', [$offer, $module, $page, $question]), [
            'question' => 'Ny?',
            'options' => ['Ja', 'Nej'],
            'correct_option' => 0,
        ])
        ->assertRedirect();

    expect($question->fresh()->question)->toBe('Ny?');
});

test('admin can delete a quiz question', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();
    $question = OfferPageQuizQuestion::factory()->for($page, 'page')->create();

    $this->actingAs($admin)
        ->delete(route('offers.modules.pages.questions.destroy', [$offer, $module, $page, $question]))
        ->assertRedirect();

    expect(OfferPageQuizQuestion::find($question->id))->toBeNull();
});

test('quiz question requires at least 2 options', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.questions.store', [$offer, $module, $page]), [
            'question' => 'Hvad?',
            'options' => ['Kun én'],
            'correct_option' => 0,
        ])
        ->assertSessionHasErrors('options');
});

test('quiz question correct option is validated as integer min 0', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $module = OfferModule::factory()->for($offer)->create();
    $page = OfferPage::factory()->for($module, 'module')->create();

    $this->actingAs($admin)
        ->post(route('offers.modules.pages.questions.store', [$offer, $module, $page]), [
            'question' => 'Hvad?',
            'options' => ['A', 'B'],
            'correct_option' => -1,
        ])
        ->assertSessionHasErrors('correct_option');
});
