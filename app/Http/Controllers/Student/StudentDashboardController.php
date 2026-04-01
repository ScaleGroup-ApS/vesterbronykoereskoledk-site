<?php

namespace App\Http\Controllers\Student;

use App\Actions\Payments\CalculateBalance;
use App\Actions\Progression\BuildStudentJourney;
use App\Actions\Progression\CheckExamReadiness;
use App\Actions\Student\BuildStudentLessonProgress;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\DrivingSkill;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CurriculumTopic;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    public function index(
        Request $request,
        CheckExamReadiness $readiness,
        CalculateBalance $balance,
        BuildStudentJourney $buildJourney,
        BuildStudentLessonProgress $buildProgress,
    ): Response {
        $student = $this->student($request);

        return Inertia::render('student/index', [
            'pendingEnrollment' => $this->pendingEnrollment($student),
            'booking' => $this->nextHighlight($student),
            'journey' => $buildJourney->handle($student),
            'readiness' => $readiness->handle($student),
            'lesson_progress' => $buildProgress->handle($student),
            'balance' => $balance->handle($student),
            'materials' => $this->materials($student),
            'curriculum_by_lesson' => $this->curriculumByLesson($student),
            'next_theory_topic' => $this->nextTheoryTopic($student),
        ]);
    }

    public function forloeb(
        Request $request,
        CheckExamReadiness $readiness,
        CalculateBalance $balance,
        BuildStudentJourney $buildJourney,
        BuildStudentLessonProgress $buildProgress,
    ): Response {
        $student = $this->student($request);

        return Inertia::render('student/forloeb', [
            'past_bookings' => $this->pastBookings($student),
            'journey' => $buildJourney->handle($student),
            'readiness' => $readiness->handle($student),
            'lesson_progress' => $buildProgress->handle($student),
            'balance' => $balance->handle($student),
            'materials' => $this->materials($student),
            'curriculum_by_lesson' => $this->curriculumByLesson($student),
        ]);
    }

    public function historik(Request $request): Response
    {
        $student = $this->student($request);

        return Inertia::render('student/historik', [
            'past_bookings' => $this->pastBookingsWithExtras($student),
        ]);
    }

    public function materiale(Request $request): Response
    {
        $student = $this->student($request);
        $student->load(['offers' => fn ($q) => $q->with('media')]);

        $completedTheoryCount = $student->bookings()
            ->where('type', BookingType::TheoryLesson->value)
            ->where('status', BookingStatus::Completed->value)
            ->count();

        $materials = $student->offers->flatMap(fn ($offer) => $offer->getMedia('materials')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'size' => $media->human_readable_size,
            'url' => route('student.offers.materials.show', [$offer->id, $media->id]),
            'offer_name' => $offer->name,
            'unlock_at_lesson' => $media->getCustomProperty('unlock_at_lesson'),
            'is_unlocked' => ((int) ($media->getCustomProperty('unlock_at_lesson') ?? 0)) <= $completedTheoryCount,
        ]))->values()->all();

        return Inertia::render('student/materiale', [
            'materials' => $materials,
        ]);
    }

    public function faerdigheder(Request $request): Response
    {
        $student = $this->student($request);

        $completedDrivingBookings = $student->bookings()
            ->where('type', BookingType::DrivingLesson->value)
            ->where('status', BookingStatus::Completed->value)
            ->whereNotNull('driving_skills')
            ->get(['driving_skills']);

        $counts = collect(DrivingSkill::cases())->mapWithKeys(fn (DrivingSkill $skill) => [
            $skill->value => ['key' => $skill->value, 'label' => $skill->label(), 'count' => 0],
        ])->all();

        foreach ($completedDrivingBookings as $booking) {
            foreach ($booking->driving_skills ?? [] as $skillValue) {
                if (isset($counts[$skillValue])) {
                    $counts[$skillValue]['count']++;
                }
            }
        }

        return Inertia::render('student/faerdigheder', [
            'skills' => array_values($counts),
        ]);
    }

    private function student(Request $request): Student
    {
        $student = $request->user()->student;
        abort_unless($student, 404);

        return $student;
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string}|null
     */
    private function nextHighlight(Student $student): ?array
    {
        $tz = config('app.timezone');

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

        if ($booking && $enrollment?->course) {
            return $booking->starts_at->lte($enrollment->course->start_at)
                ? $this->formatBooking($booking, $tz)
                : $this->formatCourse($enrollment, $tz);
        }

        if ($booking) {
            return $this->formatBooking($booking, $tz);
        }

        if ($enrollment?->course) {
            return $this->formatCourse($enrollment, $tz);
        }

        return null;
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string}
     */
    private function formatBooking(Booking $booking, string $tz): array
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
    private function formatCourse(Enrollment $enrollment, string $tz): array
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

    /**
     * @return array<string, mixed>|null
     */
    private function pendingEnrollment(Student $student): ?array
    {
        $enrollment = Enrollment::query()
            ->where('student_id', $student->id)
            ->whereIn('status', [EnrollmentStatus::PendingPayment->value, EnrollmentStatus::PendingApproval->value])
            ->with('offer')
            ->first();

        if (! $enrollment) {
            return null;
        }

        return [
            'status' => $enrollment->status->value,
            'payment_method' => $enrollment->payment_method->value,
            'offer_price' => (float) $enrollment->offer->price,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function materials(Student $student): array
    {
        $student->loadMissing(['offers' => fn ($q) => $q->with('media')]);

        return $student->offers->flatMap(fn ($offer) => $offer->getMedia('materials')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->human_readable_size,
            'url' => route('student.offers.materials.show', [$offer->id, $media->id]),
            'offer_name' => $offer->name,
        ]))->values()->all();
    }

    /**
     * @return array<int, string>
     */
    private function curriculumByLesson(Student $student): array
    {
        $student->loadMissing('offers');

        return CurriculumTopic::whereIn('offer_id', $student->offers->pluck('id'))
            ->orderBy('lesson_number')
            ->get(['lesson_number', 'title'])
            ->keyBy('lesson_number')
            ->map(fn ($t) => $t->title)
            ->all();
    }

    /**
     * @return array{lesson_number: int, title: string, description: string|null}|null
     */
    private function nextTheoryTopic(Student $student): ?array
    {
        $student->loadMissing('offers');

        $completed = $student->bookings()
            ->where('type', BookingType::TheoryLesson->value)
            ->where('status', BookingStatus::Completed->value)
            ->count();

        $topic = CurriculumTopic::whereIn('offer_id', $student->offers->pluck('id'))
            ->where('lesson_number', $completed + 1)
            ->first(['lesson_number', 'title', 'description']);

        return $topic ? $topic->only(['lesson_number', 'title', 'description']) : null;
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
     * @return list<array<string, mixed>>
     */
    private function pastBookingsWithExtras(Student $student): array
    {
        $tz = config('app.timezone');

        return $student->bookings()
            ->with('instructor:id,name')
            ->orderByDesc('starts_at')
            ->limit(50)
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
                    'instructor_note' => $b->instructor_note,
                    'driving_skills' => $b->driving_skills ?? [],
                    'instructor_name' => $b->instructor?->name,
                ];
            })
            ->values()
            ->all();
    }
}
