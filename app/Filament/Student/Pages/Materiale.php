<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Materiale extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Materiale';

    protected static ?string $title = 'Kursusmateriale';

    protected string $view = 'filament.student.pages.materiale';

    protected static ?int $navigationSort = 5;
}
