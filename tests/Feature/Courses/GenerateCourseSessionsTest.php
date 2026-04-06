<?php

use App\Actions\Courses\GenerateCourseSessions;
use App\Models\Course;

test('it generates sessions for given weekdays and time range', function () {
    // Monday 2026-04-06 to Sunday 2026-04-19 — schedule Mon+Wed 18:00-20:00
    $course = Course::factory()->create([
        'start_at' => '2026-04-06 09:00:00',
        'end_at' => '2026-04-19 17:00:00',
        'theory_schedule' => [
            'weekdays' => [1, 3], // Mon, Wed
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => '2026-04-19',
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);

    $sessions = $course->sessions()->orderBy('starts_at')->get();

    // Mon 6, Wed 8, Mon 13, Wed 15 = 4 sessions
    expect($sessions)->toHaveCount(4);
    expect($sessions[0]->starts_at->format('Y-m-d H:i'))->toBe('2026-04-06 18:00');
    expect($sessions[0]->ends_at->format('Y-m-d H:i'))->toBe('2026-04-06 20:00');
    expect($sessions[0]->session_number)->toBe(1);
    expect($sessions[1]->starts_at->format('Y-m-d H:i'))->toBe('2026-04-08 18:00');
    expect($sessions[1]->session_number)->toBe(2);
    expect($sessions[3]->session_number)->toBe(4);
});

test('it does not generate sessions when no theory_schedule is set', function () {
    $course = Course::factory()->create(['theory_schedule' => null]);

    app(GenerateCourseSessions::class)->handle($course);

    expect($course->sessions()->count())->toBe(0);
});

test('it clears existing sessions before regenerating', function () {
    $course = Course::factory()->create([
        'start_at' => '2026-04-06 09:00:00',
        'end_at' => '2026-04-19 17:00:00',
        'theory_schedule' => [
            'weekdays' => [1],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => '2026-04-19',
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);
    expect($course->sessions()->count())->toBe(2);

    // Regenerate — should still be 2, not 4
    app(GenerateCourseSessions::class)->handle($course);
    expect($course->sessions()->count())->toBe(2);
});
