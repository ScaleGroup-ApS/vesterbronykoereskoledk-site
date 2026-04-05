<?php

namespace App\Filament\Admin\Resources\BlogPosts;

use App\Filament\Admin\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Admin\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Admin\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogPost;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Blogindlæg';

    protected static ?string $modelLabel = 'Blogindlæg';

    protected static ?string $pluralModelLabel = 'Blogindlæg';

    protected static string|UnitEnum|null $navigationGroup = 'Indhold';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Indhold')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                                if ($operation !== 'create' || blank($state)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(BlogPost::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Textarea::make('excerpt')
                            ->label('Uddrag')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('body')
                            ->label('Indhold')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Publicering')
                    ->columns(2)
                    ->schema([
                        Toggle::make('published')
                            ->label('Publiceret')
                            ->default(false),

                        DateTimePicker::make('published_at')
                            ->label('Publiceringstidspunkt')
                            ->nullable()
                            ->native(false),
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

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('published')
                    ->label('Publiceret')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('published_at')
                    ->label('Publiceret den')
                    ->dateTime('d. M Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Oprettet')
                    ->dateTime('d. M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('published')
                    ->label('Publiceret')
                    ->trueLabel('Kun publicerede')
                    ->falseLabel('Kun kladder')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
