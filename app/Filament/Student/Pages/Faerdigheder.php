<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Faerdigheder extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Færdigheder';

    protected static ?string $title = 'Færdigheder';

    protected string $view = 'filament.student.pages.faerdigheder';

    protected static ?int $navigationSort = 4;
}
