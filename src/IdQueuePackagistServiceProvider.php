<?php

namespace IdQueue\IdQueuePackagist;

use IdQueue\IdQueuePackagist\Commands\ExportModelsCommand;
use Illuminate\Support\ServiceProvider;

class IdQueuePackagistServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Bind the singleton for ModelLister
        $this->app->singleton(ModelLister::class, function ($app) {
            return new ModelLister;
        });

        // Merge the package configuration
        $this->mergeConfigFrom(
            __DIR__.'/config/idqueuepackagist.php',
            'idqueuepackagist'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
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
