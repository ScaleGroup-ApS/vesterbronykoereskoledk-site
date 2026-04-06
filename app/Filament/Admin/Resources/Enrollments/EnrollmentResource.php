<?php

namespace App\Filament\Admin\Resources\Enrollments;

use App\Actions\Enrollment\ApproveEnrollment;
use App\Actions\Enrollment\RejectEnrollment;
use App\Enums\EnrollmentPaymentMethod;
use App\Enums\EnrollmentStatus;
use App\Filament\Admin\Resources\Enrollments\Pages\ListEnrollments;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Tilmeldinger';

    protected static ?string $modelLabel = 'Tilmelding';

    protected static ?string $pluralModelLabel = 'Tilmeldinger';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Elev')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('offer.name')
                    ->label('Pakke')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Betalingsmetode')
                    ->badge()
                    ->color(fn (EnrollmentPaymentMethod $state): string => match ($state) {
                        EnrollmentPaymentMethod::Stripe => 'primary',
                        EnrollmentPaymentMethod::Cash => 'success',
                    })
                    ->formatStateUsing(fn (EnrollmentPaymentMethod $state): string => match ($state) {
                        EnrollmentPaymentMethod::Stripe => 'Stripe',
                        EnrollmentPaymentMethod::Cash => 'Kontant',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (EnrollmentStatus $state): string => match ($state) {
                        EnrollmentStatus::PendingPayment => 'warning',
                        EnrollmentStatus::PendingApproval => 'warning',
                        EnrollmentStatus::Completed => 'success',
                        EnrollmentStatus::Rejected => 'danger',
                    })
                    ->formatStateUsing(fn (EnrollmentStatus $state): string => match ($state) {
                        EnrollmentStatus::PendingPayment => 'Afventer betaling',
                        EnrollmentStatus::PendingApproval => 'Afventer godkendelse',
                        EnrollmentStatus::Completed => 'Godkendt',
                        EnrollmentStatus::Rejected => 'Afvist',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oprettet')
                    ->dateTime('d. M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        EnrollmentStatus::PendingPayment->value => 'Afventer betaling',
                        EnrollmentStatus::PendingApproval->value => 'Afventer godkendelse',
                        EnrollmentStatus::Completed->value => 'Godkendt',
                        EnrollmentStatus::Rejected->value => 'Afvist',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Godkend')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Godkend tilmelding')
                    ->modalDescription('Er du sikker på, at du vil godkende denne tilmelding?')
                    ->modalSubmitActionLabel('Godkend')
                    ->visible(fn (Enrollment $record): bool => $record->status === EnrollmentStatus::PendingApproval)
                    ->action(function (Enrollment $record): void {
                        app(ApproveEnrollment::class)->handle($record, auth()->user());

                        Notification::make()
                            ->title('Tilmelding godkendt')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Afvis')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->modalHeading('Afvis tilmelding')
                    ->modalSubmitActionLabel('Afvis')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Årsag')
                            ->required()
                            ->rows(4),
                    ])
                    ->visible(fn (Enrollment $record): bool => $record->status === EnrollmentStatus::PendingApproval)
                    ->action(function (Enrollment $record, array $data): void {
                        app(RejectEnrollment::class)->handle($record, $data['rejection_reason']);

                        Notification::make()
                            ->title('Tilmelding afvist')
                            ->success()
                            ->send();
                    }),
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
            'index' => ListEnrollments::route('/'),
        ];
    }
}
