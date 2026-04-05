<?php

namespace App\Filament\Admin\Pages\Settings;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class Password extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected string $view = 'filament.admin.pages.settings.password';

    protected static ?string $navigationLabel = 'Adgangskode';

    protected static ?string $title = 'Skift adgangskode';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('current_password')
                    ->label('Nuværende adgangskode')
                    ->password()
                    ->required(),

                TextInput::make('password')
                    ->label('Ny adgangskode')
                    ->password()
                    ->required()
                    ->rule(PasswordRule::default()),

                TextInput::make('password_confirmation')
                    ->label('Bekræft ny adgangskode')
                    ->password()
                    ->required()
                    ->same('password'),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => __('Den nuværende adgangskode er forkert.'),
            ]);
        }

        $user->update([
            'password' => $data['password'],
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Adgangskode opdateret')
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
