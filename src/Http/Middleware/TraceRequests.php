<?php

namespace IdQueue\IdQueuePackagist\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraceRequests
{
    public function handle(Request $request, Closure $next)
    {
        $tracer = app('opentelemetry.tracer');
        $rootSpan = $tracer->spanBuilder('http_request')->startSpan();
        $scope = $rootSpan->activate();

        try {
            $rootSpan->setAttribute('http.method', $request->getMethod())
                ->setAttribute('http.url', $request->fullUrl())
                ->setAttribute('http.user_agent', $request->header('User-Agent'))
                ->setAttribute('http.headers', json_encode($request->headers->all()))
                ->setAttribute('http.body', $request->getContent());

            $response = $next($request);

            $rootSpan->setAttribute('http.status_code', $response->getStatusCode())
                ->setAttribute('http.response_headers', json_encode($response->headers->all()))
                ->setAttribute('http.response_body', $response instanceof JsonResponse ? $response->getContent() : '');

            $rootSpan->addEvent('request_handled', [
                'method' => $request->getMethod(),
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
            ]);

            return $response;
        } finally {
            $rootSpan->end();
            $scope->detach();
        }
    }
}
