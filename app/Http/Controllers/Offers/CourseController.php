<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function store(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $validated = $request->validate([
            'start_at' => ['required', 'date', 'after:now'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'featured_on_home' => ['sometimes', 'boolean'],
        ]);

        $validated['featured_on_home'] = $request->boolean('featured_on_home');

        $hours = (int) config('courses.default_duration_hours', 8);
        $validated['end_at'] = Carbon::parse($validated['start_at'])->addHours($hours);

        $course = $offer->courses()->create($validated);

        if ($validated['featured_on_home']) {
            Course::query()->whereKeyNot($course->id)->update(['featured_on_home' => false]);
        }

        return redirect()->route('offers.edit', $offer)
            ->with('success', 'Kursus oprettet.');
    }

    public function destroy(Offer $offer, Course $course): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $course->delete();

        return redirect()->route('offers.edit', $offer)
            ->with('success', 'Kursus slettet.');
    }
}
