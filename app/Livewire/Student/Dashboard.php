<?php

namespace App\Livewire\Student;

use App\Actions\Payments\CalculateBalance;
use App\Actions\Student\FindNextStudentEvent;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\CurriculumTopic;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(
        FindNextStudentEvent $findNextEvent,
        CalculateBalance $calculateBalance,
    ): View {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return view('livewire.student.dashboard', [
                'student' => null,
                'nextEvent' => null,
                'nextTheoryTopic' => null,
                'pendingEnrollment' => null,
                'balance' => null,
                'userName' => auth()->user()?->name,
            ]);
        }

        return view('livewire.student.dashboard', [
            'student' => $student,
            'nextEvent' => $findNextEvent->handle($student),
            'nextTheoryTopic' => $this->nextTheoryTopic($student),
            'pendingEnrollment' => $this->pendingEnrollment($student),
            'balance' => $calculateBalance->handle($student),
            'userName' => auth()->user()?->name,
        ]);
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
     * @return array{status: string, payment_method: string, offer_price: float}|null
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
}
