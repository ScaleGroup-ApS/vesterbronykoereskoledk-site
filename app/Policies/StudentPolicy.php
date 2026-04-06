<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $user->isStudent() && $user->student?->id === $student->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function download(User $user, Student $student, Media $media): bool
    {
        return $user->isAdmin()
            || $user->isInstructor()
            || ($user->isStudent() && $user->student?->id === $student->id);
    }
}
