<?php

namespace App\Providers;

use App\Policies\MediaPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Pennant\Feature;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->isLocal()) {
            URL::forceScheme('https');
        }

        Gate::policy(Media::class, MediaPolicy::class);

        Http::macro('crm', fn () => Http::baseUrl((string) config('services.crm.url'))->asJson());

        $this->defineFeatureFlags();
        $this->configureDefaults();
    }

    /**
     * Define feature flags via Laravel Pennant.
     * Defaults fall back to env vars (k8s ConfigMap) when no DB override exists.
     */
    protected function defineFeatureFlags(): void
    {
        Feature::define('two-factor', fn () => (bool) env('FEATURE_TWO_FACTOR', true));
        Feature::define('student-calendar', fn () => (bool) env('FEATURE_STUDENT_CALENDAR', true));
        Feature::define('student-chat', fn () => (bool) env('FEATURE_STUDENT_CHAT', true));
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
