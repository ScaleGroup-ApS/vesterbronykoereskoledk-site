<?php

namespace App\Filament\Admin\Resources\CurriculumTopics\Pages;

use App\Filament\Admin\Resources\CurriculumTopics\CurriculumTopicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCurriculumTopics extends ListRecords
{
    protected static string $resource = CurriculumTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
