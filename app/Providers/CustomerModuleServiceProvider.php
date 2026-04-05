<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CustomerModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $slug = config('customer.slug');

        if (! $slug) {
            return;
        }

        // Convert CUSTOMER_SLUG to StudlyCase for the PHP namespace:
        // "koereskole-aarhus" → "KoereskoleAarhus"
        // Directory: app/Customers/KoereskoleAarhus/ServiceProvider.php
        $studly = Str::studly($slug);

        $class = "App\\Customers\\{$studly}\\ServiceProvider";

        if (class_exists($class)) {
            $this->app->register($class);
        }

        $viewPath = app_path("Customers/{$studly}/resources/views");

        if (is_dir($viewPath)) {
            $this->callAfterResolving('view', function ($view) use ($viewPath) {
                $view->prependLocation($viewPath);
            });
        }
    }
}
