<?php

use App\Models\CurriculumTopic;
use App\Models\Offer;
use App\Models\User;

it('admin can list curriculum topics for an offer', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $offer = Offer::factory()->create();
    CurriculumTopic::factory()->for($offer)->create(['lesson_number' => 1, 'title' => 'Trafikken']);

    $this->actingAs($admin)
        ->get(route('curriculum.index', $offer))
        ->assertInertia(fn ($page) => $page
            ->component('curriculum/index')
            ->has('topics', 1)
        );
});

it('admin can create a curriculum topic', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->post(route('curriculum.store', $offer), [
            'lesson_number' => 1,
            'title' => 'Vigepligt',
            'description' => 'Hvem har vigepligt?',
        ])
        ->assertRedirect();

    expect(CurriculumTopic::where('offer_id', $offer->id)->where('title', 'Vigepligt')->exists())->toBeTrue();
});

it('admin can update a curriculum topic', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $topic = CurriculumTopic::factory()->create(['title' => 'Old title']);

    $this->actingAs($admin)
        ->put(route('curriculum.update', $topic), ['title' => 'New title', 'lesson_number' => $topic->lesson_number])
        ->assertRedirect();

    expect($topic->fresh()->title)->toBe('New title');
});

it('admin can delete a curriculum topic', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $topic = CurriculumTopic::factory()->create();

    $this->actingAs($admin)
        ->delete(route('curriculum.destroy', $topic))
        ->assertRedirect();

    expect(CurriculumTopic::find($topic->id))->toBeNull();
});

it('rejects duplicate lesson numbers for the same offer', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $offer = Offer::factory()->create();
    CurriculumTopic::factory()->for($offer)->create(['lesson_number' => 1]);

    $this->actingAs($admin)
        ->post(route('curriculum.store', $offer), ['lesson_number' => 1, 'title' => 'Duplicate'])
        ->assertSessionHasErrors('lesson_number');
});
