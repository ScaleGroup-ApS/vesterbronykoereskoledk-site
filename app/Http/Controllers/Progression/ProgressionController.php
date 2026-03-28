<?php

namespace App\Http\Controllers\Progression;

use App\Actions\Payments\CalculateBalance;
use App\Actions\Progression\BuildStudentJourney;
use App\Actions\Progression\CheckExamReadiness;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Inertia\Inertia;
use Inertia\Response;

class ProgressionController extends Controller
{
    public function show(
        Student $student,
        CheckExamReadiness $readiness,
        CalculateBalance $balance,
        BuildStudentJourney $buildStudentJourney,
    ): Response {
        $this->authorize('view', $student);

        $student->load(['user', 'offers']);

        return Inertia::render('progression/show', [
            'student' => $student,
            'readiness' => $readiness->handle($student),
            'balance' => $balance->handle($student),
            'journey' => $buildStudentJourney->handle($student),
        ]);
    }
}
