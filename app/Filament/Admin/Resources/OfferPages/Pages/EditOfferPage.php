<?php

namespace App\Filament\Admin\Resources\OfferPages\Pages;

use App\Filament\Admin\Resources\OfferPages\OfferPageResource;
use App\Models\OfferPage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\EditRecord;

class EditOfferPage extends EditRecord
{
    protected static string $resource = OfferPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadBanner')
                ->label('Upload banner')
                ->icon('heroicon-o-photo')
                ->form([
                    FileUpload::make('banner')
                        ->label('Bannerbillede')
                        ->image()
                        ->disk('public')
                        ->directory('offer-page-banners')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->maxSize(5120),
                ])
                ->action(function (array $data): void {
                    /** @var OfferPage $record */
                    $record = $this->getRecord();

                    if (! empty($data['banner'])) {
                        $record->clearMediaCollection('banner');
                        $record->addMediaFromDisk($data['banner'], 'public')
                            ->toMediaCollection('banner');
                    }
                }),

            Action::make('uploadVideo')
                ->label('Upload video')
                ->icon('heroicon-o-video-camera')
                ->form([
                    FileUpload::make('video')
                        ->label('Video')
                        ->disk('public')
                        ->directory('offer-page-videos')
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/avi', 'video/webm'])
                        ->maxSize(204800),
                ])
                ->action(function (array $data): void {
                    /** @var OfferPage $record */
                    $record = $this->getRecord();

                    if (! empty($data['video'])) {
                        $record->clearMediaCollection('video');
                        $record->addMediaFromDisk($data['video'], 'public')
                            ->toMediaCollection('video');
                    }
                }),

            DeleteAction::make(),
        ];
    }
}
