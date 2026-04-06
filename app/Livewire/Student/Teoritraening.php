<?php

namespace App\Livewire\Student;

use App\Models\OfferPageQuizQuestion;
use App\Models\Student;
use App\Models\TheoryPracticeAttempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

class Teoritraening extends Component
{
    public string $step = 'index';

    /** @var array<int, array{id: int, question: string, options: array<int, string>}> */
    public array $questions = [];

    /** @var array<int, int|null> Keyed by question index, value is selected option index */
    public array $answers = [];

    public int $durationSeconds = 0;

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public function render(): View
    {
        $student = auth()->user()?->student;

        if ($this->step === 'index') {
            return view('livewire.student.teoritraening', [
                'step' => 'index',
                'attempts' => $student ? $this->recentAttempts($student) : [],
                'stats' => $student ? $this->stats($student) : null,
                'hasQuestions' => $student ? $this->questionPool($student)->exists() : false,
            ]);
        }

        if ($this->step === 'exam') {
            return view('livewire.student.teoritraening', [
                'step' => 'exam',
                'questions' => $this->questions,
                'answers' => $this->answers,
                'timeLimitSeconds' => 1500,
            ]);
        }

        return view('livewire.student.teoritraening', [
            'step' => 'result',
            'result' => $this->result,
            'questions' => $this->questions,
            'answers' => $this->answers,
        ]);
    }

    public function startExam(): void
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return;
        }

        $pool = $this->questionPool($student)
            ->inRandomOrder()
            ->limit(25)
            ->get()
            ->map(fn (OfferPageQuizQuestion $q) => [
                'id' => $q->id,
                'question' => $q->question,
                'options' => $q->options,
            ])
            ->values()
            ->all();

        if (empty($pool)) {
            return;
        }

        $this->questions = $pool;
        $this->answers = array_fill(0, count($pool), null);
        $this->durationSeconds = 0;
        $this->step = 'exam';
    }

    public function submitExam(int $durationSeconds): void
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student || empty($this->questions)) {
            return;
        }

        $questionIds = array_column($this->questions, 'id');
        $questionsById = OfferPageQuizQuestion::whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $score = 0;
        foreach ($questionIds as $i => $qId) {
            $question = $questionsById->get($qId);
            if ($question && isset($this->answers[$i]) && $this->answers[$i] === $question->correct_option) {
                $score++;
            }
        }

        $total = count($questionIds);

        $attempt = TheoryPracticeAttempt::create([
            'student_id' => $student->id,
            'score' => $score,
            'total' => $total,
            'duration_seconds' => min($durationSeconds, 1800),
            'answers' => array_values($this->answers),
            'question_ids' => $questionIds,
            'attempted_at' => now(),
        ]);

        $this->result = [
            'id' => $attempt->id,
            'score' => $score,
            'total' => $total,
            'percentage' => $total > 0 ? round($score / $total * 100) : 0,
            'duration_seconds' => $attempt->duration_seconds,
            'questions_with_answers' => collect($questionIds)
                ->map(function (int $qId, int $i) use ($questionsById) {
                    $q = $questionsById->get($qId);

                    return $q ? [
                        'question' => $q->question,
                        'options' => $q->options,
                        'correct_option' => $q->correct_option,
                        'explanation' => $q->explanation,
                        'student_answer' => $this->answers[$i] ?? null,
                    ] : null;
                })
                ->filter()
                ->values()
                ->all(),
        ];

        $this->step = 'result';
    }

    public function reset(): void
    {
        $this->step = 'index';
        $this->questions = [];
        $this->answers = [];
        $this->result = null;
        $this->durationSeconds = 0;
    }

    /**
     * @return list<array{score: int, total: int, percentage: int, duration_seconds: int, attempted_at: string}>
     */
    private function recentAttempts(Student $student): array
    {
        return $student->theoryPracticeAttempts()
            ->orderByDesc('attempted_at')
            ->limit(10)
            ->get()
            ->map(fn (TheoryPracticeAttempt $a) => [
                'score' => $a->score,
                'total' => $a->total,
                'percentage' => $a->total > 0 ? round($a->score / $a->total * 100) : 0,
                'duration_seconds' => $a->duration_seconds,
                'attempted_at' => $a->attempted_at->timezone(config('app.timezone'))->translatedFormat('d. M Y \k\l. H:i'),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{total_attempts: int, pass_count: int, best_score: int|null, available_questions: int}
     */
    private function stats(Student $student): array
    {
        $total = $student->theoryPracticeAttempts()->count();

        $passCount = $student->theoryPracticeAttempts()
            ->whereRaw('score * 100 >= total * 90')
            ->count();

        $bestScore = $total > 0
            ? (int) $student->theoryPracticeAttempts()
                ->selectRaw('ROUND(CAST(score AS FLOAT) / total * 100) as pct')
                ->orderByDesc('pct')
                ->value('pct')
            : null;

        return [
            'total_attempts' => $total,
            'pass_count' => $passCount,
            'best_score' => $bestScore,
            'available_questions' => $this->questionPool($student)->count(),
        ];
    }

    /** @return Builder<OfferPageQuizQuestion> */
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
}
