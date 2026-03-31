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

        $byType = [];

        foreach ($readiness['required'] as $typeKey => $required) {
            if ($required <= 0) {
                continue;
            }

            $completed = (int) ($readiness['completed'][$typeKey] ?? 0);
            $scheduled = (int) ($scheduledByType[$typeKey] ?? 0);
            $remaining = max(0, $required - $completed - $scheduled);

            $enumType = BookingType::tryFrom($typeKey);
            $label = $enumType ? $enumType->label() : $typeKey;

            $byType[$typeKey] = [
                'type' => $typeKey,
                'label' => $label,
                'required' => $required,
                'completed' => $completed,
                'scheduled' => $scheduled,
                'remaining' => $remaining,
            ];
        }

        $ordered = [];

        foreach (self::TYPE_ORDER as $typeKey) {
            if (isset($byType[$typeKey])) {
                $ordered[] = $byType[$typeKey];
                unset($byType[$typeKey]);
            }
        }

        foreach ($byType as $row) {
            $ordered[] = $row;
        }

        return $ordered;
    }
}
