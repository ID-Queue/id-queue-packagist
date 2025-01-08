<?php

namespace IdQueue\IdQueuePackagist;

use IdQueue\IdQueuePackagist\Commands\ExportModelsCommand;
use IdQueue\IdQueuePackagist\Http\Middleware\AuthServices;
use IdQueue\IdQueuePackagist\Http\Middleware\TraceRequests;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OpenTelemetry\API\LoggerHolder as APILoggerHolder;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

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

        // Register AuthServices middleware
        $this->app['router']->aliasMiddleware('auth.services', AuthServices::class);

        // Merge the package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/config/idqueuepackagist.php',
            'idqueuepackagist'
        );

        // Merge OpenTelemetry configuration
        $this->mergeConfigFrom(
            __DIR__ . '/config/opentelemetry.php',
            'opentelemetry'
        );

        // Register OpenTelemetry tracer
        $this->registerOpenTelemetryTracer();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the timezone globally for Laravel and PHP
        $timezone = 'America/New_York'; // EST timezone
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        // Load package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish migrations to the host application
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'id-queue-migrations');

        // Publish configuration files
        $this->publishes([
            __DIR__ . '/config/idqueuepackagist.php' => config_path('idqueuepackagist.php'),
            __DIR__ . '/config/opentelemetry.php' => config_path('opentelemetry.php'),
        ], 'id-queue-config');

        // Register console commands if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([ExportModelsCommand::class]);
        }

        // Register middleware
        $this->registerMiddleware();
    }

    /**
     * Register OpenTelemetry tracer.
     */
    protected function registerOpenTelemetryTracer(): void
    {
        // Retrieve configuration values
        $otlpHttpEndpoint = config('opentelemetry.endpoint');
        $otlpServiceName = config('opentelemetry.service_name');
        $otlpInsecure = config('opentelemetry.insecure');

        // Set up logger for OpenTelemetry
        APILoggerHolder::set(new Logger('php-otlp-example', [new StreamHandler('php://stderr')]));

        // Create OTLP transport and exporter
        $transport = (new OtlpHttpTransportFactory)->create($otlpHttpEndpoint, 'application/x-protobuf');
        $exporter = new SpanExporter($transport);

        // Create the Tracer provider and assign exporter
        $tracerProvider = new TracerProvider(new SimpleSpanProcessor($exporter));
        $this->app->instance('opentelemetry.tracer', $tracerProvider->getTracer($otlpServiceName));
    }

    /**
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddlewareToGroup('api', TraceRequests::class);
    }
}
