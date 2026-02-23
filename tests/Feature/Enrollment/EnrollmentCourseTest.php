<?php

use App\Models\Course;
use App\Models\EnrollmentRequest;
use App\Models\Offer;
use App\Models\Student;

test('enrollment request belongs to a course', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $student = Student::factory()->create();

    $request = EnrollmentRequest::factory()->create([
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'student_id' => $student->id,
    ]);

    expect($request->course)->toBeInstanceOf(Course::class);
    expect($request->course->id)->toBe($course->id);
});

test('enrollment request course_id can be null', function () {
    $student = Student::factory()->create();
    $offer = Offer::factory()->create();

    $request = EnrollmentRequest::factory()->create([
        'offer_id' => $offer->id,
        'student_id' => $student->id,
        'course_id' => null,
    ]);

    expect($request->course)->toBeNull();
});
