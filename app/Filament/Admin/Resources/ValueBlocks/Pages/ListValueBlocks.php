<?php

namespace App\Filament\Admin\Resources\ValueBlocks\Pages;

use App\Filament\Admin\Resources\ValueBlocks\ValueBlockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListValueBlocks extends ListRecords
{
    protected static string $resource = ValueBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
