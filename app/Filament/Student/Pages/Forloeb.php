<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Forloeb extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Mit forløb';

    protected static ?string $title = 'Mit forløb';

    protected string $view = 'filament.student.pages.forloeb';

    protected static ?int $navigationSort = 2;
}
