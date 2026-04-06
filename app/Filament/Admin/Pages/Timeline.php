<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Timeline extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected string $view = 'filament.admin.pages.timeline';

    protected static ?string $navigationLabel = 'Tidslinje';

    protected static ?string $title = 'Tidslinje';
}
