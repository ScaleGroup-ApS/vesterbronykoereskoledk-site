<?php

namespace App\Filament\Admin\Resources\OfferPages\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'quizQuestions';

    protected static ?string $title = 'Quiz-spørgsmål';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->label('Spørgsmål')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),

                Repeater::make('options')
                    ->label('Svarmuligheder')
                    ->simple(
                        TextInput::make('option')
                            ->label('Svarmulighed')
                            ->required()
                    )
                    ->minItems(2)
                    ->maxItems(6)
                    ->defaultItems(4)
                    ->reorderable(false)
                    ->columnSpanFull(),

                TextInput::make('correct_option')
                    ->label('Korrekt svar (0-baseret index)')
                    ->helperText('0 = første svarmulighed, 1 = anden, osv.')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->required(),

                Textarea::make('explanation')
                    ->label('Forklaring (vises ved forkert svar)')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Rækkefølge')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('question')
                    ->label('Spørgsmål')
                    ->limit(80)
                    ->searchable(),

                TextColumn::make('options')
                    ->label('Svarmuligheder')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) : 0)
                    ->suffix(' svar'),

                TextColumn::make('correct_option')
                    ->label('Korrekt index')
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
