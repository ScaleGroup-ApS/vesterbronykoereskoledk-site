<?php

namespace App\Http\Controllers\Students;

use App\Enums\DrivingSkill;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentSkillController extends Controller
{
    public function __invoke(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $validated = $request->validate([
            'skills' => ['present', 'array'],
            'skills.*' => [Rule::in(array_column(DrivingSkill::cases(), 'value'))],
        ]);

        $student->update([
            'completed_skills' => $validated['skills'],
        ]);

        return back();
    }
}
