<?php

namespace App\Filament\Admin\Resources\ContactDetails;

use App\Filament\Admin\Resources\ContactDetails\Pages\CreateContactDetail;
use App\Filament\Admin\Resources\ContactDetails\Pages\EditContactDetail;
use App\Filament\Admin\Resources\ContactDetails\Pages\ListContactDetails;
use App\Models\MarketingContactDetail;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ContactDetailResource extends Resource
{
    protected static ?string $model = MarketingContactDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?string $navigationLabel = 'Kontaktoplysninger';

    protected static ?string $modelLabel = 'Kontaktoplysninger';

    protected static ?string $pluralModelLabel = 'Kontaktoplysninger';

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kontaktoplysninger')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone_href')
                            ->label('Telefon (href)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('fx "+4512345678"'),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->required()
                            ->email()
                            ->maxLength(255),

                        TextInput::make('opening_hours')
                            ->label('Åbningstider')
                            ->nullable()
                            ->maxLength(255),

                        TextInput::make('address_line')
                            ->label('Adresse')
                            ->nullable()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                TextColumn::make('opening_hours')
                    ->label('Åbningstider')
                    ->placeholder('—'),

                TextColumn::make('address_line')
                    ->label('Adresse')
                    ->placeholder('—'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactDetails::route('/'),
            'create' => CreateContactDetail::route('/create'),
            'edit' => EditContactDetail::route('/{record}/edit'),
        ];
    }
}
