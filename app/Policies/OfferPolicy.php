<?php

namespace App\Policies;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Offer $offer): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Offer $offer): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $user->isAdmin();
    }

    public function download(User $user, Offer $offer, Media $media): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $user->student !== null
            && $user->student->offers()->where('offers.id', $offer->id)->exists();
    }

    public function learnContent(User $user, Offer $offer): Response|bool
    {
        if (! $user->student) {
            return Response::deny('Student profile not found.', 404);
        }

        return Enrollment::query()
            ->where('student_id', $user->student->id)
            ->where('offer_id', $offer->id)
            ->where('status', EnrollmentStatus::Completed)
            ->exists();
    }
}
