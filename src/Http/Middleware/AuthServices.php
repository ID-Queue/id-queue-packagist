<?php

namespace IdQueue\IdQueuePackagist\Http\Middleware;

use Closure;
use IdQueue\IdQueuePackagist\Services\ConnectionService;
use IdQueue\IdQueuePackagist\Utils\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthServices
{
    /**
     * Handle an incoming request.
     *
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->header('Authorization');

        // Check for authorization token
        if (empty($bearer)) {
            return $this->errorResponse('Authorization token is required!');
        }

        $jwt = str_replace('Bearer ', '', $bearer);

        // Validate JWT format
        if (empty($jwt)) {
            return $this->errorResponse('Auth token is invalid!');
        }

        // Validate JWT data
        $jwtData = Helper::isJwtValid($jwt);
        if (empty($jwtData['check']) || empty($jwtData['details'])) {
            return $this->errorResponse('Token has expired or is invalid!');
        }

        $user = json_decode($jwtData['details']);

        // Validate user data
        if (empty($user->Company_DB) || empty($user->id)) {
            return $this->errorResponse('Auth token is invalid!');
        }

        // Set connection and authenticate user
        ConnectionService::setConnection($user);
        $request->merge((array) $user);
        auth()->loginUsingId($user->id);

        return $next($request);
    }

    /**
     * Return a standardized error response.
     */
    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ]);
    }
}
