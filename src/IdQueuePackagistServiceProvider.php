<?php

namespace IdQueue\IdQueuePackagist;

use IdQueue\IdQueuePackagist\Commands\ExportModelsCommand;
use IdQueue\IdQueuePackagist\Http\Middleware\AuthServices;
use Illuminate\Support\ServiceProvider;

class IdQueuePackagistServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the singleton for ModelLister
        $this->app->singleton(ModelLister::class, function ($app) {
            return new ModelLister;
        });
        $this->app['router']->aliasMiddleware('auth.services', AuthServices::class);

        // Merge the package configuration
        $this->mergeConfigFrom(
            __DIR__.'/config/idqueuepackagist.php',
            'idqueuepackagist'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the timezone globally for Laravel and PHP
        $timezone = 'America/New_York'; // EST timezone

        // Update Laravel configuration
        config(['app.timezone' => $timezone]);

        // Set PHP's default timezone
        date_default_timezone_set($timezone);
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish migrations to the host application
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'id-queue-migrations');

        // Publish configuration file
        $this->publishes([
            __DIR__.'/config/idqueuepackagist.php' => config_path('idqueuepackagist.php'),
        ], 'id-queue-config');

        // Register console commands if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                ExportModelsCommand::class,
            ]);
        }
    }
}
