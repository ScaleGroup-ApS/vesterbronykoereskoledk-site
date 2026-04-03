<?php

namespace App\Http\Controllers\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseAttendanceController extends Controller
{
    public function __invoke(Request $request, Course $course, Enrollment $enrollment): RedirectResponse
    {
        $this->authorize('update', $course->offer);

        $enrollment->update([
            'attended' => ! $enrollment->attended,
        ]);

        return back();
    }
}
