<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Oversigt';

    protected string $view = 'filament.student.pages.dashboard';

    protected static ?int $navigationSort = 1;
}
