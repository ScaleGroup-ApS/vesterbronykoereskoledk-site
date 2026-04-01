<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    /**
     * Generic checks that apply to all media, regardless of owner or ability.
     * Add expiry, rate-limiting, audit-logging, or other cross-cutting concerns here.
     * Owner-specific access rules live in each owner model's policy as `download()`.
     */
    public function view(User $user, Media $media): bool
    {
        // Soft-deleted owner → never serve files, regardless of role.
        if (method_exists($media->model, 'trashed') && $media->model->trashed()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, Media $media): bool
    {
        if ($media->model instanceof Student) {
            return $user->isAdmin();
        }

        return $user->isAdmin() || $user->isInstructor();
    }
}
