<?php

namespace App\Filament\Admin\Resources\Bookings;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\UserRole;
use App\Filament\Admin\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Admin\Resources\Bookings\Pages\EditBooking;
use App\Filament\Admin\Resources\Bookings\Pages\ListBookings;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Bookinger';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Bookinger';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booking')
                    ->columns(2)
                    ->schema([
                        Select::make('student_id')
                            ->label('Elev')
                            ->relationship('student', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? "Elev #{$record->id}")
                            ->searchable(['user.name'])
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Type')
                            ->options(
                                collect(BookingType::cases())
                                    ->mapWithKeys(fn (BookingType $type) => [$type->value => $type->label()])
                                    ->toArray()
                            )
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                BookingStatus::Scheduled->value => 'Planlagt',
                                BookingStatus::Completed->value => 'Gennemført',
                                BookingStatus::Cancelled->value => 'Annulleret',
                                BookingStatus::NoShow->value => 'Udeblevet',
                            ])
                            ->default(BookingStatus::Scheduled->value)
                            ->required(),

                        Select::make('instructor_id')
                            ->label('Instruktør')
                            ->options(
                                User::whereIn('role', [UserRole::Instructor->value, UserRole::Admin->value])
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),

                        Select::make('vehicle_id')
                            ->label('Køretøj')
                            ->options(
                                Vehicle::where('active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->nullable(),

                        DateTimePicker::make('starts_at')
                            ->label('Starter')
                            ->required()
                            ->native(false)
                            ->seconds(false),

                        DateTimePicker::make('ends_at')
                            ->label('Slutter')
                            ->required()
                            ->native(false)
                            ->seconds(false),

                        Textarea::make('notes')
                            ->label('Noter')
                            ->columnSpanFull()
                            ->rows(3)
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Elev')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (BookingType $state): string => match ($state) {
                        BookingType::TheoryLesson => 'info',
                        BookingType::DrivingLesson => 'success',
                        BookingType::TheoryExam => 'warning',
                        BookingType::PracticalExam => 'warning',
                        BookingType::TrackDriving => 'success',
                        BookingType::SlipperyDriving => 'success',
                    })
                    ->formatStateUsing(fn (BookingType $state): string => $state->label()),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (BookingStatus $state): string => match ($state) {
                        BookingStatus::Scheduled => 'gray',
                        BookingStatus::Completed => 'success',
                        BookingStatus::NoShow => 'danger',
                        BookingStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (BookingStatus $state): string => match ($state) {
                        BookingStatus::Scheduled => 'Planlagt',
                        BookingStatus::Completed => 'Gennemført',
                        BookingStatus::NoShow => 'Udeblevet',
                        BookingStatus::Cancelled => 'Annulleret',
                    }),

                TextColumn::make('starts_at')
                    ->label('Starter')
                    ->dateTime('d. M Y H:i')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Varighed (min.)')
                    ->getStateUsing(fn (Booking $record): int => (int) $record->starts_at->diffInMinutes($record->ends_at))
                    ->numeric()
                    ->sortable(false),

                TextColumn::make('instructor.name')
                    ->label('Instruktør')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('vehicle.name')
                    ->label('Køretøj')
                    ->sortable()
                    ->placeholder('-'),

                IconColumn::make('attended')
                    ->label('Mødt')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(
                        collect(BookingType::cases())
                            ->mapWithKeys(fn (BookingType $type) => [$type->value => $type->label()])
                            ->toArray()
                    ),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        BookingStatus::Scheduled->value => 'Planlagt',
                        BookingStatus::Completed->value => 'Gennemført',
                        BookingStatus::Cancelled->value => 'Annulleret',
                        BookingStatus::NoShow->value => 'Udeblevet',
                    ]),

                SelectFilter::make('instructor_id')
                    ->label('Instruktør')
                    ->options(
                        User::whereIn('role', [UserRole::Instructor->value, UserRole::Admin->value])
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('mark_attended')
                    ->label('Mødt')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Marker som mødt')
                    ->modalDescription('Er du sikker på at eleven mødte op?')
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Scheduled && $record->attended !== true)
                    ->action(function (Booking $record): void {
                        $record->update([
                            'attended' => true,
                            'status' => BookingStatus::Completed,
                            'attendance_recorded_at' => now(),
                            'attendance_recorded_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Elev markeret som mødt')
                            ->success()
                            ->send();
                    }),
                Action::make('mark_no_show')
                    ->label('Udeblevet')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Marker som udeblevet')
                    ->modalDescription('Er du sikker på at eleven udeblev?')
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Scheduled && $record->attended !== false)
                    ->action(function (Booking $record): void {
                        $record->update([
                            'attended' => false,
                            'status' => BookingStatus::NoShow,
                            'attendance_recorded_at' => now(),
                            'attendance_recorded_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Elev markeret som udeblevet')
                            ->warning()
                            ->send();
                    }),
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
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
