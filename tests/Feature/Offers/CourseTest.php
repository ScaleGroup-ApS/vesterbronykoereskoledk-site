<?php

use App\Models\Course;
use App\Models\Offer;
use App\Models\User;

test('admin can create a course from the courses store', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->from(route('courses.index'))
        ->post(route('courses.store'), [
            'offer_id' => $offer->id,
            'start_at' => '2026-06-01 09:00:00',
            'featured_on_home' => false,
        ])
        ->assertRedirect();

    expect(Course::count())->toBe(1);
    $created = Course::first();
    expect($created->start_at->format('Y-m-d'))->toBe('2026-06-01');
    expect($created->offer_id)->toBe($offer->id);
    $hours = (int) config('courses.default_duration_hours', 8);
    expect($created->end_at->timestamp)->toBe($created->start_at->copy()->addHours($hours)->timestamp);
});

test('admin cannot create a course for an addon offer', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->addon()->create();

    $this->actingAs($admin)
        ->post(route('courses.store'), [
            'offer_id' => $offer->id,
            'start_at' => '2026-06-01 09:00:00',
        ])
        ->assertSessionHasErrors('offer_id');
});

test('admin can delete a course from the courses destroy route', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();

    $this->actingAs($admin)
        ->delete(route('courses.destroy', $course))
        ->assertRedirect(route('courses.index'));

    expect(Course::find($course->id))->toBeNull();
});

test('instructor cannot create a course', function () {
    $instructor = User::factory()->instructor()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($instructor)
        ->post(route('courses.store'), [
            'offer_id' => $offer->id,
            'start_at' => '2026-06-01 09:00:00',
        ])
        ->assertForbidden();
});

test('course start_at must be in the future', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();

    $this->actingAs($admin)
        ->post(route('courses.store'), [
            'offer_id' => $offer->id,
            'start_at' => '2020-01-01 09:00:00',
        ])
        ->assertSessionHasErrors('start_at');
});

test('marking a course as featured clears other featured courses', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $first = Course::factory()->for($offer)->create(['featured_on_home' => true]);
    $second = Course::factory()->for($offer)->create(['featured_on_home' => false]);

    $this->actingAs($admin)
        ->patch(route('courses.update', $second), [
            'start_at' => $second->start_at->format('Y-m-d H:i:s'),
            'max_students' => null,
            'public_spots_remaining' => null,
            'featured_on_home' => true,
        ])
        ->assertRedirect(route('courses.show', $second));

    expect($first->fresh()->featured_on_home)->toBeFalse();
    expect($second->fresh()->featured_on_home)->toBeTrue();
});
