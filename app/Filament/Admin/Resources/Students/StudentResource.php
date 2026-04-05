<?php

namespace App\Filament\Admin\Resources\Students;

use App\Actions\Student\SendStudentLoginLink;
use App\Enums\StudentStatus;
use App\Filament\Admin\Resources\Students\Pages\CreateStudent as CreateStudentPage;
use App\Filament\Admin\Resources\Students\Pages\EditStudent;
use App\Filament\Admin\Resources\Students\Pages\ListStudents;
use App\Filament\Admin\Resources\Students\Pages\ViewStudent;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Elever';

    protected static ?string $modelLabel = 'Elev';

    protected static ?string $pluralModelLabel = 'Elever';

    public static function form(Schema $schema): Schema
    {
        $isCreate = $schema->getLivewire() instanceof CreateStudentPage;

        return $schema
            ->components([
                Section::make('Stamoplysninger')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Navn')
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(false),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->unique('users', 'email', ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Adgangskode')
                            ->password()
                            ->required($isCreate)
                            ->dehydrated(false)
                            ->visibleOn('create'),

                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(50)
                            ->default(null),

                        TextInput::make('cpr')
                            ->label('CPR-nummer')
                            ->placeholder('DDMMÅÅ-XXXX')
                            ->maxLength(20)
                            ->default(null),
                    ]),

                Section::make('Status & Datoer')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                StudentStatus::Active->value => 'Aktiv',
                                StudentStatus::Inactive->value => 'Inaktiv',
                                StudentStatus::Graduated->value => 'Udlært',
                                StudentStatus::DroppedOut->value => 'Frafaldet',
                            ])
                            ->default(StudentStatus::Active->value)
                            ->required(),

                        DatePicker::make('start_date')
                            ->label('Startdato')
                            ->native(false),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kontaktoplysninger')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Navn'),

                        TextEntry::make('user.email')
                            ->label('Email'),

                        TextEntry::make('phone')
                            ->label('Telefon')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (StudentStatus $state): string => match ($state) {
                                StudentStatus::Active => 'success',
                                StudentStatus::Inactive => 'warning',
                                StudentStatus::Graduated => 'info',
                                StudentStatus::DroppedOut => 'danger',
                            })
                            ->formatStateUsing(fn (StudentStatus $state): string => match ($state) {
                                StudentStatus::Active => 'Aktiv',
                                StudentStatus::Inactive => 'Inaktiv',
                                StudentStatus::Graduated => 'Udlært',
                                StudentStatus::DroppedOut => 'Frafaldet',
                            }),

                        TextEntry::make('start_date')
                            ->label('Startdato')
                            ->date('d. M Y')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Oprettet')
                            ->dateTime('d. M Y H:i'),
                    ]),

                Section::make('Færdigheder')
                    ->schema([
                        TextEntry::make('completed_skills')
                            ->label('Gennemførte færdigheder')
                            ->formatStateUsing(function (mixed $state): string {
                                if (empty($state)) {
                                    return 'Ingen færdigheder registreret';
                                }

                                $labels = [
                                    'parking' => 'Parkering',
                                    'motorvej' => 'Motorvej',
                                    'roundabouts' => 'Rundkørsel',
                                    'city_driving' => 'Bykørsel',
                                    'overtaking' => 'Overhaling',
                                    'reversing' => 'Bakring',
                                    'lane_change' => 'Filskifte',
                                    'emergency_stop' => 'Nødstop',
                                ];

                                $items = is_array($state) ? $state : [$state];

                                return implode(', ', array_map(
                                    fn (string $key): string => $labels[$key] ?? $key,
                                    $items,
                                ));
                            })
                            ->badge()
                            ->separator(','),
                    ]),

                Section::make('Seneste bookinger')
                    ->schema([
                        RepeatableEntry::make('bookings')
                            ->label('')
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Type')
                                    ->formatStateUsing(fn (mixed $state): string => $state instanceof BackedEnum ? $state->value : (string) $state),

                                TextEntry::make('starts_at')
                                    ->label('Dato')
                                    ->dateTime('d. M Y H:i'),

                                TextEntry::make('attended')
                                    ->label('Mødt')
                                    ->formatStateUsing(fn (?bool $state): string => match ($state) {
                                        true => 'Ja',
                                        false => 'Nej',
                                        null => '-',
                                    })
                                    ->badge()
                                    ->color(fn (?bool $state): string => match ($state) {
                                        true => 'success',
                                        false => 'danger',
                                        null => 'gray',
                                    }),

                                TextEntry::make('instructor_note')
                                    ->label('Instruktørnote')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Navn')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (StudentStatus $state): string => match ($state) {
                        StudentStatus::Active => 'success',
                        StudentStatus::Inactive => 'warning',
                        StudentStatus::Graduated => 'info',
                        StudentStatus::DroppedOut => 'danger',
                    })
                    ->formatStateUsing(fn (StudentStatus $state): string => match ($state) {
                        StudentStatus::Active => 'Aktiv',
                        StudentStatus::Inactive => 'Inaktiv',
                        StudentStatus::Graduated => 'Udlært',
                        StudentStatus::DroppedOut => 'Frafaldet',
                    })
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Startdato')
                    ->date('d. M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Oprettet')
                    ->dateTime('d. M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        StudentStatus::Active->value => 'Aktiv',
                        StudentStatus::Inactive->value => 'Inaktiv',
                        StudentStatus::Graduated->value => 'Udlært',
                        StudentStatus::DroppedOut->value => 'Frafaldet',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('send_login_link')
                    ->label('Send login link')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Send login link')
                    ->modalDescription('Send et magisk login link til elevens email-adresse.')
                    ->modalSubmitActionLabel('Send')
                    ->action(function (Student $record): void {
                        app(SendStudentLoginLink::class)->handle($record);

                        Notification::make()
                            ->title('Login link sendt')
                            ->success()
                            ->send();
                    }),
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudentPage::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->with('user', 'bookings')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
