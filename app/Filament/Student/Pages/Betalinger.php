<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Betalinger extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Betalinger';

    protected static ?string $title = 'Betalinger';

    protected string $view = 'filament.student.pages.betalinger';

    protected static ?int $navigationSort = 9;
}
