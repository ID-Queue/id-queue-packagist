<?php

return [
    'endpoint' => env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4318/v1/traces'),
    'service_name' => env('OTEL_SERVICE_NAME', 'laravel-example-app'),
    'insecure' => env('OTEL_EXPORTER_OTLP_INSECURE', 'true'),
];