<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\CreateStudent;
use App\Actions\Students\DeleteStudent;
use App\Actions\Students\UpdateStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Student::class);

        $students = Student::with('user')
            ->latest()
            ->paginate(15);

        return Inertia::render('students/index', [
            'students' => $students,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Student::class);

        return Inertia::render('students/create');
    }

    public function store(StoreStudentRequest $request, CreateStudent $action): RedirectResponse
    {
        $student = $action->handle($request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Elev oprettet.');
    }

    public function show(Student $student): Response
    {
        $this->authorize('view', $student);

        $student->load('user');

        return Inertia::render('students/show', [
            'student' => $student,
        ]);
    }

    public function edit(Student $student): Response
    {
        $this->authorize('update', $student);

        $student->load('user');

        return Inertia::render('students/edit', [
            'student' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student, UpdateStudent $action): RedirectResponse
    {
        $action->handle($student, $request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Elev opdateret.');
    }

    public function destroy(Student $student, DeleteStudent $action): RedirectResponse
    {
        $this->authorize('delete', $student);

        $action->handle($student);

        return redirect()->route('students.index')
            ->with('success', 'Elev slettet.');
    }
}
