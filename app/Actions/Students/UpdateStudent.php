<?php

namespace App\Actions\Students;

use App\Models\Student;

class UpdateStudent
{
    /**
     * @param  array{name?: string, email?: string, phone?: string|null, cpr?: string|null, status?: string, start_date?: string|null}  $data
     */
    public function handle(Student $student, array $data): Student
    {
        $student->update([
            'phone' => $data['phone'] ?? $student->phone,
            'cpr' => $data['cpr'] ?? $student->cpr,
            'status' => $data['status'] ?? $student->status,
            'start_date' => $data['start_date'] ?? $student->start_date,
        ]);

        $student->user->update([
            'name' => $data['name'] ?? $student->user->name,
            'email' => $data['email'] ?? $student->user->email,
        ]);

        return $student->refresh();
    }
}
