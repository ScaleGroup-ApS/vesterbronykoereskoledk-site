<?php

namespace App\Policies;

use App\Models\MarketingHomeCopy;
use App\Models\User;

class MarketingHomeCopyPolicy
{
    public function view(User $user, MarketingHomeCopy $marketingHomeCopy): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MarketingHomeCopy $marketingHomeCopy): bool
    {
        return $user->isAdmin();
    }
}
