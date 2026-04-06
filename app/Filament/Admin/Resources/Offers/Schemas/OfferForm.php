<?php

namespace App\Filament\Admin\Resources\Offers\Schemas;

use App\Enums\OfferType;
use App\Models\Offer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Navn')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set, Get $get) {
                        if ($operation !== 'create') {
                            return;
                        }

                        if (blank($state)) {
                            return;
                        }

                        $set('slug', Offer::uniqueSlugFromName($state));
                    }),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'offers', column: 'slug', ignoreRecord: true)
                    ->rules([
                        'alpha_dash',
                    ]),

                Textarea::make('description')
                    ->label('Beskrivelse')
                    ->columnSpanFull()
                    ->rows(4)
                    ->default(null),

                Select::make('type')
                    ->label('Type')
                    ->options(OfferType::class)
                    ->required()
                    ->default(OfferType::Primary),

                TextInput::make('price')
                    ->label('Pris (DKK)')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->minValue(0)
                    ->suffix('kr.'),

                TextInput::make('theory_lessons')
                    ->label('Teoritimer')
                    ->numeric()
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->default(0),

                TextInput::make('driving_lessons')
                    ->label('Køretimer')
                    ->numeric()
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->default(0),

                Toggle::make('track_required')
                    ->label('Bane krævet')
                    ->default(false),

                Toggle::make('slippery_required')
                    ->label('Glatbane krævet')
                    ->default(false),

                Toggle::make('requires_theory_exam')
                    ->label('Kræver teoriprøve')
                    ->default(true),

                Toggle::make('requires_practical_exam')
                    ->label('Kræver køreprøve')
                    ->default(true),
            ]);
    }
}
