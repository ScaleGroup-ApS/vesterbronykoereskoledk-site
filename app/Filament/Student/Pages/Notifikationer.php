<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Notifikationer extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifikationer';

    protected static ?string $title = 'Notifikationer';

    protected string $view = 'filament.student.pages.notifikationer';

    protected static ?int $navigationSort = 8;
}
