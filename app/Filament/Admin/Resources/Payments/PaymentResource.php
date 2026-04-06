<?php

namespace App\Filament\Admin\Resources\Payments;

use App\Enums\PaymentMethod;
use App\Filament\Admin\Resources\Payments\Pages\CreatePayment;
use App\Filament\Admin\Resources\Payments\Pages\ListPayments;
use App\Models\Payment;
use App\Models\Student;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Betalinger';

    protected static ?string $modelLabel = 'Betaling';

    protected static ?string $pluralModelLabel = 'Betalinger';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Betalingsoplysninger')
                    ->columns(2)
                    ->schema([
                        Select::make('student_id')
                            ->label('Elev')
                            ->options(fn (): array => Student::query()
                                ->with('user')
                                ->get()
                                ->mapWithKeys(fn (Student $student): array => [
                                    $student->id => $student->user->name,
                                ])
                                ->all()
                            )
                            ->searchable()
                            ->required(),

                        TextInput::make('amount')
                            ->label('Beløb (DKK)')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Select::make('method')
                            ->label('Betalingsmetode')
                            ->options([
                                PaymentMethod::Cash->value => 'Kontant',
                                PaymentMethod::Card->value => 'Kort',
                                PaymentMethod::MobilePay->value => 'MobilePay',
                                PaymentMethod::Invoice->value => 'Faktura',
                            ])
                            ->required(),

                        DateTimePicker::make('recorded_at')
                            ->label('Registreret')
                            ->native(false)
                            ->default(now())
                            ->required(),

                        Textarea::make('notes')
                            ->label('Noter')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),
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

                TextColumn::make('amount')
                    ->label('Beløb')
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 2, ',', '.').' kr.')
                    ->sortable(),

                TextColumn::make('method')
                    ->label('Betalingsmetode')
                    ->badge()
                    ->color(fn (PaymentMethod $state): string => match ($state) {
                        PaymentMethod::Cash => 'success',
                        PaymentMethod::Card => 'primary',
                        PaymentMethod::MobilePay => 'gray',
                        PaymentMethod::Invoice => 'gray',
                    })
                    ->formatStateUsing(fn (PaymentMethod $state): string => match ($state) {
                        PaymentMethod::Cash => 'Kontant',
                        PaymentMethod::Card => 'Kort',
                        PaymentMethod::MobilePay => 'MobilePay',
                        PaymentMethod::Invoice => 'Faktura',
                    }),

                TextColumn::make('notes')
                    ->label('Noter')
                    ->limit(50)
                    ->placeholder('-'),

                TextColumn::make('recorded_at')
                    ->label('Registreret')
                    ->dateTime('d. M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('recorded_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
        ];
    }
}
