<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Support extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected string $view = 'filament.admin.pages.support';

    protected static ?string $navigationLabel = 'Support';

    protected static ?string $title = 'Support';

    protected static ?int $navigationSort = 90;
}
