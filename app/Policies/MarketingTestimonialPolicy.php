<?php

namespace App\Policies;

use App\Models\MarketingTestimonial;
use App\Models\User;

class MarketingTestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, MarketingTestimonial $marketingTestimonial): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MarketingTestimonial $marketingTestimonial): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, MarketingTestimonial $marketingTestimonial): bool
    {
        return $user->isAdmin();
    }
}
