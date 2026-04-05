<?php

namespace App\Livewire\Student;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\CurriculumTopic;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $student = $this->student();

        if (! $student) {
            return view('livewire.student.dashboard', [
                'student' => null,
                'nextBooking' => null,
                'nextTheoryTopic' => null,
                'userName' => auth()->user()?->name,
            ]);
        }

        return view('livewire.student.dashboard', [
            'student' => $student,
            'nextBooking' => $this->nextBooking($student),
            'nextTheoryTopic' => $this->nextTheoryTopic($student),
            'userName' => auth()->user()?->name,
        ]);
    }

    private function student(): ?Student
    {
        return auth()->user()?->student;
    }

    /**
     * @return array{title: string, range_label: string}|null
     */
    private function nextBooking(Student $student): ?array
    {
        $tz = config('app.timezone');

        $booking = Booking::query()
            ->where('student_id', $student->id)
            ->where('starts_at', '>=', now())
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->orderBy('starts_at')
            ->first();

        if (! $booking) {
            return null;
        }

        $start = $booking->starts_at->timezone($tz);
        $end = $booking->ends_at->timezone($tz);

        return [
            'title' => $booking->type->label(),
            'range_label' => $start->translatedFormat('l j. F Y').' · '.$start->format('H:i').'–'.$end->format('H:i'),
        ];
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
}
