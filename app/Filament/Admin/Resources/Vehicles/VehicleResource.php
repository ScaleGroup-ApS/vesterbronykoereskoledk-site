<?php

namespace App\Filament\Admin\Resources\Vehicles;

use App\Filament\Admin\Resources\Vehicles\Pages\CreateVehicle;
use App\Filament\Admin\Resources\Vehicles\Pages\EditVehicle;
use App\Filament\Admin\Resources\Vehicles\Pages\ListVehicles;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Køretøjer';

    protected static ?string $modelLabel = 'Køretøj';

    protected static ?string $pluralModelLabel = 'Køretøjer';

    protected static string|UnitEnum|null $navigationGroup = 'Indstillinger';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Oplysninger')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Navn')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('plate_number')
                            ->label('Nummerplade')
                            ->required()
                            ->maxLength(20),

                        Toggle::make('active')
                            ->label('Aktiv')
                            ->default(true),
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

                TextColumn::make('plate_number')
                    ->label('Nummerplade')
                    ->searchable(),

                ToggleColumn::make('active')
                    ->label('Aktiv'),
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
            'index' => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'edit' => EditVehicle::route('/{record}/edit'),
        ];
    }
}
