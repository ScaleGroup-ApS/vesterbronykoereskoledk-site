<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Teoritraening extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Teoriøvelse';

    protected static ?string $title = 'Teoriøvelse';

    protected string $view = 'filament.student.pages.teoritraening';

    protected static ?int $navigationSort = 3;
}
