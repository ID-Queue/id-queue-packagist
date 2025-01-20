<?php

namespace IdQueue\IdQueuePackagist\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraceRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Only proceed if OpenTelemetry is enabled
        if ((bool) config('opentelemetry.enable', false)) {

            // Initialize tracer and span
            $tracer = app('opentelemetry.tracer');
            $rootSpan = $tracer->spanBuilder('http_request')->startSpan();
            $scope = $rootSpan->activate();

            try {
                // Set request attributes
                $rootSpan->setAttribute('http.method', $request->getMethod())
                    ->setAttribute('http.url', $request->fullUrl())
                    ->setAttribute('http.user_agent', $request->header('User-Agent'))
                    ->setAttribute('http.headers', json_encode($request->headers->all()))
                    ->setAttribute('http.body', json_encode($request->all()));

                // Pass the request to the next middleware and get the response
                $response = $next($request);

                // If the response is null, initialize an empty response object
                if (! $response) {
                    $response = response();
                }

                // Set response attributes
                $rootSpan->setAttribute('http.status_code', $response->getStatusCode())
                    ->setAttribute('http.response_headers', json_encode($response->headers->all()))
                    ->setAttribute('http.response_body', $response instanceof JsonResponse ? $response->getContent() : '');

                // Add an event for the request being handled
                $rootSpan->addEvent('request_handled', [
                    'method' => $request->getMethod(),
                    'url' => $request->fullUrl(),
                    'status_code' => $response->getStatusCode(),
                ]);

                return $response;

            } catch (\Exception $e) {
                // Add an error event to the span if any exception occurs
                $rootSpan->addEvent('request_error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e; // Re-throw the exception after logging
            } finally {
                // Always end the span and detach the scope
                $rootSpan->end();
                $scope->detach();
            }
        }

        // Return the next response if OpenTelemetry is disabled
        return $next($request);
    }
}
