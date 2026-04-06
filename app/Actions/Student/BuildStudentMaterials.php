<?php

namespace App\Actions\Student;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Student;

class BuildStudentMaterials
{
    /**
     * Build the list of materials available to a student, with unlock state.
     *
     * Materials are locked until the student has completed a minimum number of
     * theory lessons (controlled by the `unlock_at_lesson` media custom property).
     * A value of 0 means always unlocked.
     *
     * @return list<array{id: int, name: string, file_name: string, mime_type: string, size: string, url: string, offer_name: string, unlock_at_lesson: int|null, is_unlocked: bool}>
     */
    public function handle(Student $student): array
    {
        $student->loadMissing(['offers' => fn ($q) => $q->with('media')]);

        $completedTheoryCount = $student->bookings()
            ->where('type', BookingType::TheoryLesson->value)
            ->where('status', BookingStatus::Completed->value)
            ->count();

        return $student->offers->flatMap(fn ($offer) => $offer->getMedia('materials')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->human_readable_size,
            'url' => route('student.offers.materials.show', [$offer->id, $media->id]),
            'offer_name' => $offer->name,
            'unlock_at_lesson' => $media->getCustomProperty('unlock_at_lesson'),
            'is_unlocked' => ((int) ($media->getCustomProperty('unlock_at_lesson') ?? 0)) <= $completedTheoryCount,
        ]))->values()->all();
    }
}
