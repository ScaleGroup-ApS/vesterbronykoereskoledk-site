<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Historik extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?string $navigationLabel = 'Historik';

    protected static ?string $title = 'Historik';

    protected string $view = 'filament.student.pages.historik';

    protected static ?int $navigationSort = 5;
}
