<?php

namespace App\Actions\Progression;

use App\Enums\BookingStatus;
use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Student;
use Carbon\Carbon;

class BuildStudentJourney
{
    /** @var list<string> */
    private const STEP_ORDER = [
        'theory_lesson',
        'track_driving',
        'slippery_driving',
        'driving_lesson',
        'theory_exam',
        'practical_exam',
    ];

    public function __construct(
        private CheckExamReadiness $readiness,
    ) {}

    /**
     * @return array{
     *     steps: list<array{
     *         key: string,
     *         label: string,
     *         status: 'done'|'in_progress'|'upcoming',
     *         detail: string|null,
     *         at: string|null
     *     }>,
     *     upcoming_bookings: list<array{
     *         id: int,
     *         type: string,
     *         type_label: string,
     *         status: string,
     *         starts_at: string,
     *         ends_at: string,
     *         starts_at_local: string,
     *         ends_at_local: string
     *     }>
     * }
     */
    public function handle(Student $student): array
    {
        $student->loadMissing('offers');

        $readiness = $this->readiness->handle($student);

        $enrollment = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', EnrollmentStatus::Completed)
            ->with(['course', 'offer'])
            ->latest()
            ->first();

        $steps = [];

        if ($enrollment?->course !== null) {
            $courseStart = $enrollment->course->start_at;
            $steps[] = [
                'key' => 'course_start',
                'label' => 'Holdstart',
                'status' => Carbon::now()->greaterThanOrEqualTo($courseStart) ? 'done' : 'upcoming',
                'detail' => $enrollment->offer?->name,
                'at' => $courseStart->toIso8601String(),
            ];
        }

        $steps = array_merge($steps, $this->stepsFromReadiness($readiness));

        $tz = config('app.timezone');

        $upcomingBookings = $student->bookings()
            ->where('starts_at', '>=', now())
            ->where('status', BookingStatus::Scheduled)
            ->orderBy('starts_at')
            ->limit(12)
            ->get()
            ->map(fn ($booking) => [
                'id' => $booking->id,
                'type' => $booking->type->value,
                'type_label' => $booking->type->label(),
                'status' => $booking->status->value,
                'starts_at' => $booking->starts_at->toIso8601String(),
                'ends_at' => $booking->ends_at->toIso8601String(),
                'starts_at_local' => $booking->starts_at->timezone($tz)->translatedFormat('l j. F Y \k\l. H:i'),
                'ends_at_local' => $booking->ends_at->timezone($tz)->format('H:i'),
            ])
            ->values()
            ->all();

        return [
            'steps' => $steps,
            'upcoming_bookings' => $upcomingBookings,
        ];
    }

    /**
     * @param  array{is_ready: bool, completed: array<string, int>, required: array<string, int>, missing: array<string, int>}  $readiness
     * @return list<array{key: string, label: string, status: 'done'|'in_progress'|'upcoming', detail: string|null, at: string|null}>
     */
    private function stepsFromReadiness(array $readiness): array
    {
        $labels = [
            'theory_lesson' => 'Teorilektioner',
            'track_driving' => 'Banekørsel',
            'slippery_driving' => 'Glat bane',
            'driving_lesson' => 'Køretimer',
            'theory_exam' => 'Teoriprøve',
            'practical_exam' => 'Køreprøve',
        ];

        $steps = [];

        foreach (self::STEP_ORDER as $key) {
            $needed = $readiness['required'][$key] ?? 0;
            if ($needed <= 0) {
                continue;
            }
            $done = $readiness['completed'][$key] ?? 0;
            if ($done >= $needed) {
                $status = 'done';
            } elseif ($done > 0) {
                $status = 'in_progress';
            } else {
                $status = 'upcoming';
            }
            $steps[] = [
                'key' => $key,
                'label' => $labels[$key],
                'status' => $status,
                'detail' => "{$done} / {$needed}",
                'at' => null,
            ];
        }

        return $steps;
    }
}
