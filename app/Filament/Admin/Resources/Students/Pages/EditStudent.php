<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Actions\Student\UpdateStudent;
use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Student $record */
        $record = $this->getRecord();
        $record->loadMissing('user');

        $data['name'] = $record->user->name;
        $data['email'] = $record->user->email;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Student $record */
        return app(UpdateStudent::class)->handle($record, [
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'cpr' => array_key_exists('cpr', $data) ? ($data['cpr'] ?: null) : null,
            'status' => $data['status'] ?? null,
            'start_date' => $data['start_date'] ?? null,
        ]);
    }
}
