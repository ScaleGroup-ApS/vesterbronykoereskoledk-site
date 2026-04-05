<?php

use App\Providers\AppServiceProvider;
use App\Providers\CustomerModuleServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\StudentPanelProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    CustomerModuleServiceProvider::class,
    AdminPanelProvider::class,
    StudentPanelProvider::class,
    FortifyServiceProvider::class,
];
