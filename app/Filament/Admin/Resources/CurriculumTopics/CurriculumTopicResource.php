<?php

namespace App\Filament\Admin\Resources\CurriculumTopics;

use App\Filament\Admin\Resources\CurriculumTopics\Pages\CreateCurriculumTopic;
use App\Filament\Admin\Resources\CurriculumTopics\Pages\EditCurriculumTopic;
use App\Filament\Admin\Resources\CurriculumTopics\Pages\ListCurriculumTopics;
use App\Models\CurriculumTopic;
use App\Models\Offer;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurriculumTopicResource extends Resource
{
    protected static ?string $model = CurriculumTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Pensum';

    protected static ?string $modelLabel = 'Pensumsemne';

    protected static ?string $pluralModelLabel = 'Pensumsemner';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Oplysninger')
                    ->columns(2)
                    ->schema([
                        Select::make('offer_id')
                            ->label('Pakke')
                            ->options(fn (): array => Offer::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),

                        TextInput::make('lesson_number')
                            ->label('Lektionsnummer')
                            ->numeric()
                            ->required(),

                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Beskrivelse')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson_number')
                    ->label('Lektion')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Beskrivelse')
                    ->limit(60)
                    ->placeholder('-'),

                TextColumn::make('offer.name')
                    ->label('Pakke')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('lesson_number');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurriculumTopics::route('/'),
            'create' => CreateCurriculumTopic::route('/create'),
            'edit' => EditCurriculumTopic::route('/{record}/edit'),
        ];
    }
}
