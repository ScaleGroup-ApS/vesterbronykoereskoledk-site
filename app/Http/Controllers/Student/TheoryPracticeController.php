<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OfferPageQuizQuestion;
use App\Models\Student;
use App\Models\TheoryPracticeAttempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TheoryPracticeController extends Controller
{
    public function index(Request $request): Response
    {
        $student = $this->student($request);

        $attempts = $student->theoryPracticeAttempts()
            ->orderByDesc('attempted_at')
            ->limit(20)
            ->get()
            ->map(fn (TheoryPracticeAttempt $a) => [
                'id' => $a->id,
                'score' => $a->score,
                'total' => $a->total,
                'percentage' => $a->total > 0 ? round(($a->score / $a->total) * 100) : 0,
                'duration_seconds' => $a->duration_seconds,
                'attempted_at' => $a->attempted_at->toIso8601String(),
            ]);

        $totalAttempts = $student->theoryPracticeAttempts()->count();
        $passCount = $student->theoryPracticeAttempts()
            ->whereRaw('score * 100 >= total * 90')
            ->count();

        $bestScore = $totalAttempts > 0
            ? $student->theoryPracticeAttempts()
                ->selectRaw('ROUND(CAST(score AS FLOAT) / total * 100) as pct')
                ->orderByDesc('pct')
                ->value('pct')
            : null;

        $availableCount = $this->questionPoolCount($student);

        return Inertia::render('student/theory-practice', [
            'attempts' => $attempts,
            'stats' => [
                'total_attempts' => $totalAttempts,
                'pass_count' => $passCount,
                'best_score' => $bestScore,
                'available_questions' => $availableCount,
            ],
        ]);
    }

    public function start(Request $request): Response
    {
        $student = $this->student($request);

        $questions = $this->questionPool($student)
            ->inRandomOrder()
            ->limit(25)
            ->get()
            ->map(fn (OfferPageQuizQuestion $q) => [
                'id' => $q->id,
                'question' => $q->question,
                'options' => $q->options,
            ]);

        abort_if($questions->isEmpty(), 404, 'Ingen spørgsmål tilgængelige.');

        return Inertia::render('student/theory-practice-exam', [
            'questions' => $questions,
            'time_limit_seconds' => 1500,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $student = $this->student($request);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'min:0'],
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['required', 'integer', 'exists:offer_page_quiz_questions,id'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
        ]);

        $questionsById = OfferPageQuizQuestion::whereIn('id', $validated['question_ids'])
            ->get()
            ->keyBy('id');

        $score = 0;
        foreach ($validated['question_ids'] as $i => $qId) {
            $question = $questionsById->get($qId);
            if ($question && isset($validated['answers'][$i]) && $validated['answers'][$i] === $question->correct_option) {
                $score++;
            }
        }

        $attempt = TheoryPracticeAttempt::create([
            'student_id' => $student->id,
            'score' => $score,
            'total' => $questionsById->count(),
            'duration_seconds' => min($validated['duration_seconds'], 1800),
            'answers' => $validated['answers'],
            'question_ids' => $validated['question_ids'],
            'attempted_at' => now(),
        ]);

        return redirect()->route('student.theory-practice.result', $attempt);
    }

    public function result(Request $request, TheoryPracticeAttempt $attempt): Response
    {
        $student = $this->student($request);
        abort_unless($attempt->student_id === $student->id, 403);

        $questionsById = OfferPageQuizQuestion::whereIn('id', $attempt->question_ids)
            ->get()
            ->keyBy('id');

        $questions = collect($attempt->question_ids)
            ->values()
            ->map(fn (int $qId, int $i) => $questionsById->get($qId))
            ->filter()
            ->values()
            ->map(fn (OfferPageQuizQuestion $q, int $i) => [
                'id' => $q->id,
                'question' => $q->question,
                'options' => $q->options,
                'correct_option' => $q->correct_option,
                'explanation' => $q->explanation,
                'student_answer' => $attempt->answers[$i] ?? null,
            ]);

        return Inertia::render('student/theory-practice-result', [
            'attempt' => [
                'id' => $attempt->id,
                'score' => $attempt->score,
                'total' => $attempt->total,
                'percentage' => $attempt->total > 0 ? round(($attempt->score / $attempt->total) * 100) : 0,
                'duration_seconds' => $attempt->duration_seconds,
                'attempted_at' => $attempt->attempted_at->toIso8601String(),
            ],
            'questions' => $questions,
        ]);
    }

    private function student(Request $request): Student
    {
        $student = $request->user()->student;
        abort_unless($student, 404);

        return $student;
    }

    /**
     * @return Builder<OfferPageQuizQuestion>
     */
    private function questionPool(Student $student): Builder
    {
        $student->loadMissing('offers.modules.pages');

        $pageIds = $student->offers->flatMap(
            fn ($offer) => $offer->modules->flatMap(
                fn ($module) => $module->pages->pluck('id')
            )
        );

        return OfferPageQuizQuestion::whereIn('offer_page_id', $pageIds);
    }

    private function questionPoolCount(Student $student): int
    {
        return $this->questionPool($student)->count();
    }
}
