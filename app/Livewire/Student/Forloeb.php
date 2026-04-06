<?php

namespace App\Livewire\Student;

use App\Actions\Progression\BuildStudentJourney;
use App\Actions\Progression\CheckExamReadiness;
use App\Actions\Student\BuildStudentLessonProgress;
use App\Models\CurriculumTopic;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Forloeb extends Component
{
    public function render(
        CheckExamReadiness $readiness,
        BuildStudentJourney $buildJourney,
        BuildStudentLessonProgress $buildProgress,
    ): View {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return view('livewire.student.forloeb', [
                'student' => null,
                'journey' => ['steps' => [], 'upcoming_bookings' => []],
                'readiness' => ['is_ready' => false, 'completed' => [], 'required' => [], 'missing' => []],
                'lessonProgress' => [],
                'curriculumByLesson' => [],
            ]);
        }

        $student->loadMissing('offers');

        $curriculumByLesson = CurriculumTopic::whereIn('offer_id', $student->offers->pluck('id'))
            ->orderBy('lesson_number')
            ->get(['lesson_number', 'title'])
            ->keyBy('lesson_number')
            ->map(fn ($t) => $t->title)
            ->all();

        return view('livewire.student.forloeb', [
            'student' => $student,
            'journey' => $buildJourney->handle($student),
            'readiness' => $readiness->handle($student),
            'lessonProgress' => $buildProgress->handle($student),
            'curriculumByLesson' => $curriculumByLesson,
        ]);
    }
}
