<?php

namespace App\Actions\Students;

use App\Models\Student;

class DeleteStudent
{
    public function handle(Student $student): void
    {
        $student->delete();
    }
}
