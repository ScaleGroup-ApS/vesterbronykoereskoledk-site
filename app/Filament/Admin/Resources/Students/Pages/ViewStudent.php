<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Actions\Student\SendStudentLoginLink;
use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_login_link')
                ->label('Send login link')
                ->icon(Heroicon::OutlinedEnvelope)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Send login link')
                ->modalDescription('Send et magisk login link til elevens email-adresse.')
                ->modalSubmitActionLabel('Send')
                ->action(function (): void {
                    /** @var Student $record */
                    $record = $this->getRecord();

                    app(SendStudentLoginLink::class)->handle($record);

                    Notification::make()
                        ->title('Login link sendt')
                        ->success()
                        ->send();
                }),

            EditAction::make(),

            DeleteAction::make(),
        ];
    }
}
