<?php

namespace App\Policies;

use App\Models\OfferModule;
use App\Models\User;

class OfferModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, OfferModule $offerModule): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function update(User $user, OfferModule $offerModule): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function delete(User $user, OfferModule $offerModule): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }
}
