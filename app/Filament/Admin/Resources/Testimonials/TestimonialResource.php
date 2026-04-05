<?php

namespace App\Filament\Admin\Resources\Testimonials;

use App\Filament\Admin\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Admin\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Admin\Resources\Testimonials\Pages\ListTestimonials;
use App\Models\MarketingTestimonial;
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

class TestimonialResource extends Resource
{
    protected static ?string $model = MarketingTestimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Udtalelser';

    protected static ?string $modelLabel = 'Udtalelse';

    protected static ?string $pluralModelLabel = 'Udtalelser';

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Udtalelse')
                    ->columns(2)
                    ->schema([
                        TextInput::make('author_name')
                            ->label('Navn')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('author_detail')
                            ->label('Detalje om forfatter')
                            ->nullable()
                            ->maxLength(255)
                            ->placeholder('fx "Elev 2024"'),

                        Textarea::make('quote')
                            ->label('Citat')
                            ->required()
                            ->rows(4)
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
                TextColumn::make('author_name')
                    ->label('Navn')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quote')
                    ->label('Citat')
                    ->limit(80)
                    ->searchable(),

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
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
