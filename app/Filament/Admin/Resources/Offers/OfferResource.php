<?php

namespace App\Filament\Admin\Resources\Offers;

use App\Filament\Admin\Resources\Offers\Pages\CreateOffer;
use App\Filament\Admin\Resources\Offers\Pages\EditOffer;
use App\Filament\Admin\Resources\Offers\Pages\ListOffers;
use App\Filament\Admin\Resources\Offers\RelationManagers\CoursesRelationManager;
use App\Filament\Admin\Resources\Offers\RelationManagers\ModulesRelationManager;
use App\Filament\Admin\Resources\Offers\Schemas\OfferForm;
use App\Filament\Admin\Resources\Offers\Tables\OffersTable;
use App\Models\Offer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Pakker';

    protected static ?string $modelLabel = 'Pakke';

    protected static ?string $pluralModelLabel = 'Pakker';

    public static function form(Schema $schema): Schema
    {
        return OfferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OffersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ModulesRelationManager::class,
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOffers::route('/'),
            'create' => CreateOffer::route('/create'),
            'edit' => EditOffer::route('/{record}/edit'),
        ];
    }
}
