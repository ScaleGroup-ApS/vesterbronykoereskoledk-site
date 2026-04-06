<?php

namespace App\Filament\Admin\Resources\OfferPages;

use App\Filament\Admin\Resources\OfferPages\Pages\CreateOfferPage;
use App\Filament\Admin\Resources\OfferPages\Pages\EditOfferPage;
use App\Filament\Admin\Resources\OfferPages\Pages\ListOfferPages;
use App\Filament\Admin\Resources\OfferPages\RelationManagers\QuizQuestionsRelationManager;
use App\Models\OfferModule;
use App\Models\OfferPage;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OfferPageResource extends Resource
{
    protected static ?string $model = OfferPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Sider';

    protected static ?string $modelLabel = 'Side';

    protected static ?string $pluralModelLabel = 'Sider';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Indhold')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('offer_module_id')
                            ->label('Modul')
                            ->options(
                                OfferModule::query()
                                    ->with('offer')
                                    ->get()
                                    ->groupBy(fn (OfferModule $module) => $module->offer?->name ?? 'Ukendt pakke')
                                    ->map(fn ($modules) => $modules->pluck('title', 'id'))
                                    ->toArray()
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        RichEditor::make('body')
                            ->label('Indhold')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'bulletList',
                                'blockquote',
                                'h2',
                                'h3',
                                'codeBlock',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Rækkefølge')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module.offer.name')
                    ->label('Pakke')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('module.title')
                    ->label('Modul')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('quiz_questions_count')
                    ->label('Quiz-spørgsmål')
                    ->counts('quizQuestions')
                    ->numeric(),

                TextColumn::make('created_at')
                    ->label('Oprettet')
                    ->dateTime('d. M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->label('Modul')
                    ->relationship('module', 'title'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            QuizQuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfferPages::route('/'),
            'create' => CreateOfferPage::route('/create'),
            'edit' => EditOfferPage::route('/{record}/edit'),
        ];
    }
}
