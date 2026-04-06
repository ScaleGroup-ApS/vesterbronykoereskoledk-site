<?php

namespace App\Filament\Admin\Resources\ContactDetails\Pages;

use App\Filament\Admin\Resources\ContactDetails\ContactDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactDetail extends CreateRecord
{
    protected static string $resource = ContactDetailResource::class;
}
