<?php

namespace App\Policies;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    public function view(User $user, Media $media): Response|bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        switch (true) {
            case $media->model instanceof Student:
                return $user->student?->is($media->model);

            case $media->model instanceof Offer:
                $student = $user->student;

                return $student !== null
                    && $student->offers()->where('offers.id', $media->model->id)->exists();

            case $media->model instanceof OfferPage:
                $offerId = OfferModule::where('id', $media->model->offer_module_id)->value('offer_id');
                $student = $user->student;

                return $student !== null
                    && Enrollment::query()
                        ->where('student_id', $student->id)
                        ->where('offer_id', $offerId)
                        ->where('status', EnrollmentStatus::Completed)
                        ->exists();

            default:
                return Response::deny('Invalid media owner', 404);
        }
    }

    public function delete(User $user, Media $media): bool
    {
        return $media->model instanceof Student && $user->isAdmin();
    }
}
