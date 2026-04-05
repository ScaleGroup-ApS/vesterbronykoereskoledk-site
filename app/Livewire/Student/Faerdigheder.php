<?php

namespace App\Livewire\Student;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\DrivingSkill;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Faerdigheder extends Component
{
    public function render(): View
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return view('livewire.student.faerdigheder', [
                'skills' => [],
                'completedSkills' => [],
            ]);
        }

        $completedDrivingBookings = $student->bookings()
            ->where('type', BookingType::DrivingLesson->value)
            ->where('status', BookingStatus::Completed->value)
            ->whereNotNull('driving_skills')
            ->get(['driving_skills']);

        $counts = collect(DrivingSkill::cases())
            ->mapWithKeys(fn (DrivingSkill $skill) => [
                $skill->value => ['key' => $skill->value, 'label' => $skill->label(), 'count' => 0],
            ])
            ->all();

        foreach ($completedDrivingBookings as $booking) {
            foreach ($booking->driving_skills ?? [] as $skillValue) {
                if (isset($counts[$skillValue])) {
                    $counts[$skillValue]['count']++;
                }
            }
        }

        return view('livewire.student.faerdigheder', [
            'skills' => array_values($counts),
            'completedSkills' => $student->completed_skills ?? [],
        ]);
    }
}
