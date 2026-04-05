<?php

namespace App\Filament\Admin\Resources\Teams;

use App\Filament\Admin\Resources\Teams\Pages\CreateTeam;
use App\Filament\Admin\Resources\Teams\Pages\EditTeam;
use App\Filament\Admin\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Hold';

    protected static ?string $modelLabel = 'Hold';

    protected static ?string $pluralModelLabel = 'Hold';

    protected static string|UnitEnum|null $navigationGroup = 'Indstillinger';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Oplysninger')
                    ->schema([
                        TextInput::make('name')
                            ->label('Navn')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Beskrivelse')
                            ->rows(3)
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Navn')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Beskrivelse')
                    ->limit(60)
                    ->placeholder('-'),

                TextColumn::make('students_count')
                    ->label('Elever')
                    ->counts('students')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
