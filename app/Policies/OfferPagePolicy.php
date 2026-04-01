<?php

namespace App\Policies;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\OfferPage;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OfferPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, OfferPage $offerPage): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function update(User $user, OfferPage $offerPage): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function delete(User $user, OfferPage $offerPage): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function download(User $user, OfferPage $offerPage, Media $media): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        if (! $user->student) {
            return false;
        }

        $offerId = $offerPage->module->offer_id;

        return Enrollment::query()
            ->where('student_id', $user->student->id)
            ->where('offer_id', $offerId)
            ->where('status', EnrollmentStatus::Completed)
            ->exists();
    }
}
