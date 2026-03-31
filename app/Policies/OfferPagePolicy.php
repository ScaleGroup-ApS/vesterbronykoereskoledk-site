<?php

namespace App\Policies;

use App\Models\OfferPage;
use App\Models\User;

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
}
