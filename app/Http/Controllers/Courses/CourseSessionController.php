<?php

namespace App\Http\Controllers\Courses;

use App\Actions\Courses\CancelCourseSession;
use App\Actions\Courses\RecordSessionAttendance;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseSessionController extends Controller
{
    public function cancel(Course $course, CourseSession $session, CancelCourseSession $action): RedirectResponse
    {
        $this->authorize('update', $course->offer);

        $action->handle($session);

        return back()->with('success', 'Teoritime aflyst.');
    }

    public function attendance(Request $request, Course $course, CourseSession $session, RecordSessionAttendance $action): RedirectResponse
    {
        $this->authorize('update', $course->offer);

        $validated = $request->validate([
            'present_student_ids' => ['present', 'array'],
            'present_student_ids.*' => ['integer'],
        ]);

        $action->handle($session, $validated['present_student_ids']);

        return back()->with('success', 'Fremmøde registreret.');
    }
}
