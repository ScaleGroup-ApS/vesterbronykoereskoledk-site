<?php

namespace App\Filament\Admin\Resources\ValueBlocks\Pages;

use App\Filament\Admin\Resources\ValueBlocks\ValueBlockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditValueBlock extends EditRecord
{
    protected static string $resource = ValueBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
