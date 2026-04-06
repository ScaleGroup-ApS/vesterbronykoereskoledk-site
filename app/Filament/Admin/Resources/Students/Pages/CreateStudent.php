<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Actions\Student\CreateStudent as CreateStudentAction;
use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Student
    {
        return app(CreateStudentAction::class)->handle([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'cpr' => filled($data['cpr'] ?? null) ? $data['cpr'] : null,
            'start_date' => $data['start_date'] ?? null,
        ]);
    }
}
