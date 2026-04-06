<?php

namespace App\Filament\Admin\Resources\Offers\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $title = 'Hold';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('start_at')
                    ->label('Startdato')
                    ->required(),

                DateTimePicker::make('end_at')
                    ->label('Slutdato')
                    ->required(),

                TextInput::make('max_students')
                    ->label('Maks. elever')
                    ->numeric()
                    ->required()
                    ->integer()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_at')
            ->columns([
                TextColumn::make('start_at')
                    ->label('Startdato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('end_at')
                    ->label('Slutdato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('max_students')
                    ->label('Maks. elever')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('enrollments_count')
                    ->label('Tilmeldte')
                    ->counts('enrollments')
                    ->numeric(),
            ])
            ->headerActions([
                CreateAction::make(),
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
