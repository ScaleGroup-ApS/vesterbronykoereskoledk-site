<?php

namespace App\Filament\Admin\Pages;

use App\Models\MarketingHomeCopy;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class HomeCopy extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.admin.pages.home-copy';

    protected static ?string $navigationLabel = 'Forsideindhold';

    protected static ?string $title = 'Forsideindhold';

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public ?array $data = [];

    public function mount(): void
    {
        $record = MarketingHomeCopy::query()->where('key', 'home')->first();

        $this->form->fill($record?->toArray() ?? []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Hero')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_headline_prefix')
                            ->label('Overskrift – prefix')
                            ->maxLength(255),

                        TextInput::make('hero_headline_accent')
                            ->label('Overskrift – accent')
                            ->maxLength(255),

                        Textarea::make('hero_subtitle')
                            ->label('Hero undertekst')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Hvorfor os')
                    ->columns(2)
                    ->schema([
                        TextInput::make('why_title')
                            ->label('Titel')
                            ->maxLength(255),

                        Textarea::make('why_lead')
                            ->label('Leadtekst')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Anmeldelser')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reviews_title')
                            ->label('Titel')
                            ->maxLength(255),

                        Textarea::make('reviews_lead')
                            ->label('Leadtekst')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('reviews_footnote')
                            ->label('Fodnote')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Udforsk')
                    ->columns(2)
                    ->schema([
                        TextInput::make('explore_title')
                            ->label('Titel')
                            ->maxLength(255),

                        Textarea::make('explore_lead')
                            ->label('Leadtekst')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('CTA')
                    ->columns(2)
                    ->schema([
                        TextInput::make('cta_title')
                            ->label('Titel')
                            ->maxLength(255),

                        Textarea::make('cta_lead')
                            ->label('Leadtekst')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        MarketingHomeCopy::query()->updateOrCreate(
            ['key' => 'home'],
            $data,
        );

        Notification::make()
            ->title('Gemt')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Gem')
                ->action('save'),
        ];
    }
}
