<?php

namespace App\Customers\Example;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Example customer module.
 *
 * Copy this directory to app/Customers/{CUSTOMER_ID}/ and set CUSTOMER_ID
 * in the Kubernetes ConfigMap to activate this module.
 *
 * This ServiceProvider is the entry point. You can:
 *   - Register extra Filament widgets via Filament::serving()
 *   - Override container bindings
 *   - Register event listeners
 *   - Load additional routes
 *
 * Blade views placed in app/Customers/{id}/resources/views/ are automatically
 * prepended to the view search path and override base views.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        // Example: inject a custom widget into a Filament panel
        //
        // \Filament\Facades\Filament::serving(function () {
        //     \Filament\Facades\Filament::registerWidgets([
        //         \App\Customers\Example\Filament\Widgets\ExampleWidget::class,
        //     ]);
        // });
    }
}
