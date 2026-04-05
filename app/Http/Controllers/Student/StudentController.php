<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\SendStudentLoginLink;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function sendLoginLink(Student $student, SendStudentLoginLink $action): RedirectResponse
    {
        $this->authorize('update', $student);

        $student->load('user');
        $action->handle($student);

        return back()->with('success', 'Login link sendt til '.$student->user->name.'.');
    }

    public function storeMedia(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'collection' => ['required', 'string', 'in:documents,photos'],
        ]);

        $student->addMediaFromRequest('file')
            ->toMediaCollection($request->input('collection'));

        return back()->with('success', 'Fil uploadet.');
    }
}
