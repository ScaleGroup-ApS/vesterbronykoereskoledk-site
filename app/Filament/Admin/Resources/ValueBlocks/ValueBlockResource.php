<?php

namespace App\Filament\Admin\Resources\ValueBlocks;

use App\Filament\Admin\Resources\ValueBlocks\Pages\CreateValueBlock;
use App\Filament\Admin\Resources\ValueBlocks\Pages\EditValueBlock;
use App\Filament\Admin\Resources\ValueBlocks\Pages\ListValueBlocks;
use App\Models\MarketingValueBlock;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
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

class ValueBlockResource extends Resource
{
    protected static ?string $model = MarketingValueBlock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'USP-blokke';

    protected static ?string $modelLabel = 'USP-blok';

    protected static ?string $pluralModelLabel = 'USP-blokke';

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('USP-blok')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('icon')
                            ->label('Ikon')
                            ->nullable()
                            ->maxLength(100)
                            ->placeholder('fx "check" eller Heroicon-navn'),

                        Textarea::make('body')
                            ->label('Beskrivelse')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Sorteringsrækkefølge')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktiv')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Rækkefølge')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktiv'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListValueBlocks::route('/'),
            'create' => CreateValueBlock::route('/create'),
            'edit' => EditValueBlock::route('/{record}/edit'),
        ];
    }
}
