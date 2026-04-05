<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomerModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $id = config('customer.id');

        if (! $id) {
            return;
        }

        $class = "App\\Customers\\{$id}\\ServiceProvider";

        if (class_exists($class)) {
            $this->app->register($class);
        }

        $viewPath = app_path("Customers/{$id}/resources/views");

        if (is_dir($viewPath)) {
            $this->callAfterResolving('view', function ($view) use ($viewPath) {
                $view->prependLocation($viewPath);
            });
        }
    }
}
