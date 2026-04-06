<?php

namespace App\Filament\Admin\Resources\Offers\Tables;

use App\Enums\OfferType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Navn')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (OfferType $state): string => match ($state) {
                        OfferType::Primary => 'primary',
                        OfferType::Addon => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Pris')
                    ->money('DKK', locale: 'da')
                    ->sortable(),

                TextColumn::make('theory_lessons')
                    ->label('Teoritimer')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('driving_lessons')
                    ->label('Køretimer')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oprettet')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Opdateret')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
