<?php

namespace App\Actions\Students;

use App\Models\Student;

class UpdateStudent
{
    /**
     * @param  array{first_name?: string, last_name?: string, email?: string, phone?: string, cpr?: string, status?: string, start_date?: string}  $data
     */
    public function handle(Student $student, array $data): Student
    {
        $student->update(array_filter([
            'phone' => $data['phone'] ?? null,
            'cpr' => $data['cpr'] ?? null,
            'status' => $data['status'] ?? null,
            'start_date' => $data['start_date'] ?? null,
        ], fn ($value) => $value !== null));

        $userUpdates = array_filter([
            'name' => isset($data['first_name'], $data['last_name'])
                ? $data['first_name'].' '.$data['last_name']
                : null,
            'email' => $data['email'] ?? null,
        ], fn ($value) => $value !== null);

        if ($userUpdates) {
            $student->user->update($userUpdates);
        }

        return $student->refresh();
    }
}
