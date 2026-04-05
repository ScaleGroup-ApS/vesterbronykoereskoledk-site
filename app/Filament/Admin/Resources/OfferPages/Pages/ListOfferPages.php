<?php

namespace App\Filament\Admin\Resources\OfferPages\Pages;

use App\Filament\Admin\Resources\OfferPages\OfferPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfferPages extends ListRecords
{
    protected static string $resource = OfferPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
