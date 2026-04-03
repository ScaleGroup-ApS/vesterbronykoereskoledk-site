<?php

namespace App\Http\Controllers\Courses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Courses\StoreCourseRequest;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Offer::class);

        $courses = Course::query()
            ->with('offer')
            ->withCount('enrollments')
            ->upcoming()
            ->orderBy('start_at')
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'start_at' => $course->start_at->toIso8601String(),
                'end_at' => $course->end_at->toIso8601String(),
                'offer' => ['id' => $course->offer->id, 'name' => $course->offer->name],
                'enrollments_count' => $course->enrollments_count,
                'max_students' => $course->max_students,
                'featured_on_home' => $course->featured_on_home,
                'public_spots_remaining' => $course->public_spots_remaining,
            ]);

        $offers = Offer::query()->primary()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('courses/index', [
            'courses' => $courses,
            'offers' => $offers,
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $offer = Offer::query()->findOrFail($validated['offer_id']);
        unset($validated['offer_id']);

        $validated['featured_on_home'] = $request->boolean('featured_on_home');
        $validated = $this->applyDefaultEndAt($validated);

        $course = $offer->courses()->create($validated);

        if ($request->boolean('featured_on_home')) {
            Course::query()->whereKeyNot($course->id)->update(['featured_on_home' => false]);
        }

        return redirect()->route('courses.show', $course)
            ->with('success', 'Kursus oprettet.');
    }

    public function show(Course $course): Response
    {
        $this->authorize('view', $course->offer);

        $course->load(['offer', 'enrollments.student.user']);

        return Inertia::render('courses/show', [
            'course' => [
                'id' => $course->id,
                'start_at' => $course->start_at->toIso8601String(),
                'end_at' => $course->end_at->toIso8601String(),
                'max_students' => $course->max_students,
                'featured_on_home' => $course->featured_on_home,
                'public_spots_remaining' => $course->public_spots_remaining,
                'offer' => [
                    'id' => $course->offer->id,
                    'name' => $course->offer->name,
                ],
                'enrollments' => $course->enrollments->map(fn ($enrollment) => [
                    'id' => $enrollment->id,
                    'status' => $enrollment->status->value,
                    'payment_method' => $enrollment->payment_method->value,
                    'attended' => $enrollment->attended,
                    'student' => [
                        'id' => $enrollment->student->id,
                        'name' => $enrollment->student->user->name,
                        'email' => $enrollment->student->user->email,
                    ],
                    'attended_count' => \App\Models\Booking::where('student_id', $enrollment->student_id)
                        ->where('status', \App\Enums\BookingStatus::Completed->value)
                        ->whereNotNull('attended')
                        ->where('attended', true)
                        ->count(),
                    'total_bookings' => \App\Models\Booking::where('student_id', $enrollment->student_id)
                        ->whereNotIn('status', [\App\Enums\BookingStatus::Cancelled->value])
                        ->count(),
                ]),
            ],
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->has('featured_on_home')) {
            $validated['featured_on_home'] = $request->boolean('featured_on_home');
        }

        $validated = $this->applyDefaultEndAt($validated);

        $course->update($validated);

        if ($request->boolean('featured_on_home')) {
            Course::query()->whereKeyNot($course->id)->update(['featured_on_home' => false]);
        }

        return redirect()->route('courses.show', $course)
            ->with('success', 'Kursus opdateret.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course->offer);

        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Kursus slettet.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function applyDefaultEndAt(array $validated): array
    {
        $hours = (int) config('courses.default_duration_hours', 8);
        $start = Carbon::parse($validated['start_at']);
        $validated['end_at'] = $start->copy()->addHours($hours);

        return $validated;
    }
}
