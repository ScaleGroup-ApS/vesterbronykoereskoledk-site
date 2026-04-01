<?php

namespace App\Actions\Student;

use App\Events\StudentStatusChanged;
use App\Models\Student;

class UpdateStudent
{
    /**
     * @param  array{name?: string, email?: string, phone?: string|null, cpr?: string|null, status?: string, start_date?: string|null}  $data
     */
    public function handle(Student $student, array $data): Student
    {
        $oldStatus = $student->status?->value;

        $student->update([
            'phone' => $data['phone'] ?? $student->phone,
            'cpr' => $data['cpr'] ?? $student->cpr,
            'status' => $data['status'] ?? $student->status,
            'start_date' => $data['start_date'] ?? $student->start_date,
        ]);

        $newStatus = $student->status?->value;

        if ($oldStatus !== $newStatus && $newStatus !== null) {
            StudentStatusChanged::fire(
                student_id: $student->id,
                old_status: $oldStatus ?? 'unknown',
                new_status: $newStatus,
            );
        }

        $student->user->update([
            'name' => $data['name'] ?? $student->user->name,
            'email' => $data['email'] ?? $student->user->email,
        ]);

        return $student->refresh();
    }
}
