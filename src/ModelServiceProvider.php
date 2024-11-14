<?php

namespace IdQueue\IdQueuePackage;

use IdQueue\IdQueueModels\Commands\ExportModelsCommand;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ModelLister::class, function ($app) {
            return new ModelLister;
        });
    }

    public function boot()
    {
        // Load migrations from the package
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Allow users to publish migrations to their project
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'id-queue-migrations');
        if ($this->app->runningInConsole()) {

            $this->commands([
                ExportModelsCommand::class,
            ]);
        }
    }
}
