<?php

namespace App\Filament\Student\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Chat extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.student.pages.chat';

    protected static ?string $navigationLabel = 'Chat';

    protected static ?string $title = 'Chat';
}
