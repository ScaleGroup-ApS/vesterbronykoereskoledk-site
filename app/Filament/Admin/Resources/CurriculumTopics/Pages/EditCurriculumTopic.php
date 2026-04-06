<?php

namespace App\Filament\Admin\Resources\CurriculumTopics\Pages;

use App\Filament\Admin\Resources\CurriculumTopics\CurriculumTopicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCurriculumTopic extends EditRecord
{
    protected static string $resource = CurriculumTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
