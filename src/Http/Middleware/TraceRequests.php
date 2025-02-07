<?php

namespace IdQueue\IdQueuePackagist\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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
            $route = Route::current();
            $middlewares = $route ? $route->gatherMiddleware() : [];
            Log::info('Middlewares applied to the current route:', $middlewares);

            try {
                // Set request attributes
                $rootSpan->setAttribute('http.method', $request->getMethod())
                    ->setAttribute('http.url', $request->fullUrl())
                    ->setAttribute('http.user_agent', $request->header('User-Agent'))
                    ->setAttribute('http.headers', json_encode($request->headers->all()))
                    ->setAttribute('http.body', json_encode($request->all()));

                // Pass the request to the next middleware and get the response
                $response = $next($request);

                if (in_array('auth.services', $middlewares)) {
                    $rootSpan->setAttribute('http.auth', 'true');
                    $rootSpan->setAttribute('http.auth.data', json_encode(Auth::user()));
                }

                // Ensure response is valid before logging
                if ($response instanceof JsonResponse || method_exists($response, 'getStatusCode')) {
                    $rootSpan->setAttribute('http.status_code', $response->getStatusCode())
                        ->setAttribute('http.response_headers', json_encode($response->headers->all()))
                        ->setAttribute('http.response_body', method_exists($response, 'getContent') ? $response->getContent() : '');
                } else {
                    Log::warning('TraceRequests middleware encountered an unexpected response type.');
                }

                // Add an event for the request being handled
                $rootSpan->addEvent('request_handled', [
                    'method' => $request->getMethod(),
                    'url' => $request->fullUrl(),
                    'status_code' => $response->getStatusCode(),
                ]);

            } catch (\Exception $e) {
                // Log error but don't interfere with the response
                $rootSpan->addEvent('request_error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                Log::error('Exception in TraceRequests middleware: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
            } finally {
                // Always end the span and detach the scope
                $rootSpan->end();
                $scope->detach();
            }
            
            return $response;
        }

        // Return the next response regardless of errors
        return $next($request);
    }
}
