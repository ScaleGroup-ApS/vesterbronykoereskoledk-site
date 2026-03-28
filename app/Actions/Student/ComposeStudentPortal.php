<?php

namespace App\Actions\Student;

use App\Actions\Payments\CalculateBalance;
use App\Actions\Progression\BuildStudentJourney;
use App\Actions\Progression\CheckExamReadiness;
use App\Enums\BookingStatus;
use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;

class ComposeStudentPortal
{
    public function __construct(
        private CheckExamReadiness $readiness,
        private CalculateBalance $balance,
        private BuildStudentJourney $buildStudentJourney,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(User $user, bool $includePastBookings = false): array
    {
        $student = $user->student;

        abort_unless($student, 404);

        $student->load(['offers' => fn ($q) => $q->with('media')]);

        $booking = Booking::query()
            ->where('student_id', $student->id)
            ->where('starts_at', '>=', now())
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->orderBy('starts_at')
            ->first();

        $enrollment = Enrollment::query()
            ->where('student_id', $student->id)
            ->whereIn('status', [EnrollmentStatus::Completed->value, EnrollmentStatus::PendingApproval->value])
            ->with(['course', 'offer'])
            ->whereHas('course', fn ($q) => $q->where('start_at', '>=', now()))
            ->first();

        $nextLesson = $this->resolveNextHighlight($booking, $enrollment);

        $pendingEnrollment = Enrollment::query()
            ->where('student_id', $student->id)
            ->whereIn('status', [EnrollmentStatus::PendingPayment->value, EnrollmentStatus::PendingApproval->value])
            ->with('offer')
            ->first();

        $materials = $student->offers->flatMap(fn ($offer) => $offer->getMedia('materials')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->human_readable_size,
            'url' => route('student.offers.materials.show', [$offer->id, $media->id]),
            'offer_name' => $offer->name,
        ])
        )->values()->all();

        $props = [
            'pendingEnrollment' => $pendingEnrollment ? [
                'status' => $pendingEnrollment->status->value,
                'payment_method' => $pendingEnrollment->payment_method->value,
                'offer_price' => (float) $pendingEnrollment->offer->price,
            ] : null,
            'booking' => $nextLesson,
            'journey' => $this->buildStudentJourney->handle($student),
            'readiness' => $this->readiness->handle($student),
            'balance' => $this->balance->handle($student),
            'materials' => $materials,
        ];

        if ($includePastBookings) {
            $props['past_bookings'] = $this->pastBookings($student);
        }

        return $props;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function pastBookings(Student $student): array
    {
        $tz = config('app.timezone');

        return $student->bookings()
            ->with('instructor:id,name')
            ->orderByDesc('starts_at')
            ->limit(30)
            ->get()
            ->map(function (Booking $b) use ($tz) {
                $start = $b->starts_at->timezone($tz);
                $end = $b->ends_at->timezone($tz);

                return [
                    'id' => $b->id,
                    'type' => $b->type->value,
                    'type_label' => $b->type->label(),
                    'status' => $b->status->value,
                    'starts_at' => $b->starts_at->toIso8601String(),
                    'ends_at' => $b->ends_at->toIso8601String(),
                    'range_label' => $start->translatedFormat('d. MMM yyyy').' · '.$start->format('H:i').'–'.$end->format('H:i'),
                    'attended' => $b->attended,
                    'attendance_recorded_at' => $b->attendance_recorded_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string}|null
     */
    private function resolveNextHighlight(?Booking $booking, ?Enrollment $enrollment): ?array
    {
        $tz = config('app.timezone');

        if ($booking && $enrollment?->course) {
            if ($booking->starts_at->lte($enrollment->course->start_at)) {
                return $this->formatNextBooking($booking, $tz);
            }

            return $this->formatNextCourse($enrollment, $tz);
        }

        if ($booking) {
            return $this->formatNextBooking($booking, $tz);
        }

        if ($enrollment?->course) {
            return $this->formatNextCourse($enrollment, $tz);
        }

        return null;
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string}
     */
    private function formatNextBooking(Booking $booking, string $tz): array
    {
        $start = $booking->starts_at->timezone($tz);
        $end = $booking->ends_at->timezone($tz);

        return [
            'type' => $booking->type->value,
            'title' => $booking->type->label(),
            'starts_at' => $booking->starts_at->toIso8601String(),
            'ends_at' => $booking->ends_at->toIso8601String(),
            'range_label' => $start->translatedFormat('EEEE d. MMMM yyyy').' · '.$start->format('H:i').'–'.$end->format('H:i'),
        ];
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string}
     */
    private function formatNextCourse(Enrollment $enrollment, string $tz): array
    {
        $course = $enrollment->course;
        $start = $course->start_at->timezone($tz);
        $end = $course->end_at->timezone($tz);

        return [
            'type' => 'course_start',
            'title' => $enrollment->offer->name,
            'starts_at' => $course->start_at->toIso8601String(),
            'ends_at' => $course->end_at->toIso8601String(),
            'range_label' => $start->translatedFormat('EEEE d. MMMM yyyy').' · '.$start->format('H:i').'–'.$end->format('H:i'),
        ];
    }
}
