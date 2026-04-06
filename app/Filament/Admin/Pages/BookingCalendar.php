<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class BookingCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected string $view = 'filament.admin.pages.booking-calendar';

    protected static ?string $navigationLabel = 'Kalender';

    protected static ?string $title = 'Bookingkalender';

    protected static ?int $navigationSort = 1;
}
