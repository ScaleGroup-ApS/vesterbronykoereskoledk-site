<?php

namespace App\Policies;

use App\Models\MarketingValueBlock;
use App\Models\User;

class MarketingValueBlockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, MarketingValueBlock $marketingValueBlock): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MarketingValueBlock $marketingValueBlock): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, MarketingValueBlock $marketingValueBlock): bool
    {
        return $user->isAdmin();
    }
}
