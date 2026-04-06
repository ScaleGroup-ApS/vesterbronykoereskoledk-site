<?php

namespace App\Livewire\Student;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Materiale extends Component
{
    public function render(): View
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return view('livewire.student.materiale', [
                'available' => [],
                'locked' => [],
            ]);
        }

        $student->load(['offers' => fn ($q) => $q->with('media')]);

        $completedTheoryCount = $student->bookings()
            ->where('type', BookingType::TheoryLesson->value)
            ->where('status', BookingStatus::Completed->value)
            ->count();

        $allMaterials = $student->offers->flatMap(function ($offer) use ($completedTheoryCount) {
            return $offer->getMedia('materials')->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'size' => $media->human_readable_size,
                'url' => route('student.offers.materials.show', [$offer->id, $media->id]),
                'offer_name' => $offer->name,
                'unlock_at_lesson' => $media->getCustomProperty('unlock_at_lesson'),
                'is_unlocked' => ((int) ($media->getCustomProperty('unlock_at_lesson') ?? 0)) <= $completedTheoryCount,
            ]);
        });

        return view('livewire.student.materiale', [
            'available' => $allMaterials->filter(fn ($m) => $m['is_unlocked'])->values()->all(),
            'locked' => $allMaterials->filter(fn ($m) => ! $m['is_unlocked'])->values()->all(),
        ]);
    }
}
