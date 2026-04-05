<?php

namespace App\Filament\Admin\Resources\ContactDetails\Pages;

use App\Filament\Admin\Resources\ContactDetails\ContactDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactDetail extends EditRecord
{
    protected static string $resource = ContactDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
