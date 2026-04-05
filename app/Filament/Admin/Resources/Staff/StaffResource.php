<?php

namespace App\Filament\Admin\Resources\Staff;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\Staff\Pages\CreateStaff;
use App\Filament\Admin\Resources\Staff\Pages\ListStaff;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Personale';

    protected static ?string $modelLabel = 'Medarbejder';

    protected static ?string $pluralModelLabel = 'Personale';

    protected static ?string $slug = 'staff';

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

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Adgangskode')
                            ->password()
                            ->required()
                            ->visibleOn('create'),

                        Select::make('role')
                            ->label('Rolle')
                            ->options([
                                UserRole::Admin->value => 'Administrator',
                                UserRole::Instructor->value => 'Instruktør',
                            ])
                            ->required(),
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

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Rolle')
                    ->badge()
                    ->color(fn (UserRole $state): string => match ($state) {
                        UserRole::Admin => 'danger',
                        UserRole::Instructor => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (UserRole $state): string => match ($state) {
                        UserRole::Admin => 'Administrator',
                        UserRole::Instructor => 'Instruktør',
                        default => $state->value,
                    }),

                TextColumn::make('created_at')
                    ->label('Oprettet')
                    ->dateTime('d. M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', [UserRole::Admin->value, UserRole::Instructor->value]);
    }
}
