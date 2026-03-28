<?php

namespace App\Policies;

use App\Models\MarketingContactDetail;
use App\Models\User;

class MarketingContactDetailPolicy
{
    public function update(User $user, MarketingContactDetail $marketingContactDetail): bool
    {
        return $user->isAdmin();
    }
}
