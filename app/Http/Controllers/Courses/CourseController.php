<?php

namespace App\Http\Controllers\Courses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(): Response
    {
        $palette = ['#2563eb', '#16a34a', '#dc2626', '#9333ea', '#d97706', '#0891b2'];

        $courses = Course::query()
            ->with('offer')
            ->orderBy('start_at')
            ->get()
            ->map(fn (Course $course) => [
                'id' => (string) $course->id,
                'title' => $course->offer->name,
                'start' => $course->start_at->format('Y-m-d H:i'),
                'end' => $course->end_at->format('Y-m-d H:i'),
                'calendarId' => 'offer-'.$course->offer_id,
                'offer_id' => $course->offer_id,
            ]);

        $offers = Offer::query()
            ->whereHas('courses')
            ->get(['id', 'name'])
            ->values()
            ->map(fn (Offer $offer, int $index) => [
                'id' => $offer->id,
                'name' => $offer->name,
                'color' => $palette[$offer->id % count($palette)],
            ]);

        return Inertia::render('courses/index', [
            'events' => $courses,
            'offers' => $offers,
        ]);
    }

    public function show(Course $course): Response
    {
        $course->load(['offer', 'enrollments.student.user']);

        return Inertia::render('courses/show', [
            'course' => [
                'id' => $course->id,
                'start_at' => $course->start_at->toIso8601String(),
                'end_at' => $course->end_at->toIso8601String(),
                'max_students' => $course->max_students,
                'offer' => [
                    'id' => $course->offer->id,
                    'name' => $course->offer->name,
                ],
                'enrollments' => $course->enrollments->map(fn ($enrollment) => [
                    'id' => $enrollment->id,
                    'status' => $enrollment->status->value,
                    'payment_method' => $enrollment->payment_method->value,
                    'student' => [
                        'id' => $enrollment->student->id,
                        'name' => $enrollment->student->user->name,
                        'email' => $enrollment->student->user->email,
                    ],
                ]),
            ],
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('success', 'Kursus opdateret.');
    }
}
