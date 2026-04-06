<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Feedback extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Feedback';

    protected static ?string $title = 'Feedback';

    protected string $view = 'filament.student.pages.feedback';

    protected static ?int $navigationSort = 7;
}
