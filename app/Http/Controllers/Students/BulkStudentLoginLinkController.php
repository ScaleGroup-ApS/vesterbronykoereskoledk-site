<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\SendStudentLoginLink;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BulkStudentLoginLinkController extends Controller
{
    public function __invoke(Request $request, SendStudentLoginLink $action): RedirectResponse
    {
        $this->authorize('create', Student::class);

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        $students = Student::with('user')
            ->whereIn('id', $validated['student_ids'])
            ->get();

        foreach ($students as $student) {
            $action->handle($student);
        }

        $count = $students->count();

        return back()->with('success', "Login link sendt til {$count} elever.");
    }
}
