<?php

namespace App\Actions\Student;

use App\Actions\Progression\CheckExamReadiness;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Student;

class BuildStudentLessonProgress
{
    /** @var list<string> */
    private const TYPE_ORDER = [
        'theory_lesson',
        'track_driving',
        'slippery_driving',
        'driving_lesson',
        'theory_exam',
        'practical_exam',
    ];

    public function __construct(
        private CheckExamReadiness $checkExamReadiness,
    ) {}

    /**
     * @return list<array{
     *     type: string,
     *     label: string,
     *     required: int,
     *     completed: int,
     *     scheduled: int,
     *     remaining: int
     * }>
     */
    public function handle(Student $student): array
    {
        $student->loadMissing('offers');

        $readiness = $this->checkExamReadiness->handle($student);

        $scheduledByType = Booking::query()
            ->where('student_id', $student->id)
            ->where('status', BookingStatus::Scheduled)
            ->where('starts_at', '>=', now())
            ->selectRaw('type, count(*) as c')
            ->groupBy('type')
            ->pluck('c', 'type')
            ->all();

        $required = $readiness['required'];
        $rows = [];

        foreach (self::TYPE_ORDER as $typeKey) {
            if (($required[$typeKey] ?? 0) > 0) {
                $rows[] = $this->buildRow($typeKey, $required[$typeKey], $readiness['completed'], $scheduledByType);
            }
        }

        foreach (array_diff_key($required, array_flip(self::TYPE_ORDER)) as $typeKey => $req) {
            if ($req > 0) {
                $rows[] = $this->buildRow($typeKey, $req, $readiness['completed'], $scheduledByType);
            }
        }

        return $rows;
    }

    /**
     * @param  array<string, int>  $completed
     * @param  array<string, int>  $scheduled
     * @return array{type: string, label: string, required: int, completed: int, scheduled: int, remaining: int}
     */
    private function buildRow(string $typeKey, int $required, array $completed, array $scheduled): array
    {
        $done = (int) ($completed[$typeKey] ?? 0);
        $sched = (int) ($scheduled[$typeKey] ?? 0);
        $enumType = BookingType::tryFrom($typeKey);

        return [
            'type' => $typeKey,
            'label' => $enumType ? $enumType->label() : $typeKey,
            'required' => $required,
            'completed' => $done,
            'scheduled' => $sched,
            'remaining' => max(0, $required - $done - $sched),
        ];
    }
}
