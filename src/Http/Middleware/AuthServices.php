<?php

namespace IdQueue\IdQueuePackagist\Http\Middleware;

use Closure;
use IdQueue\IdQueuePackagist\Services\ConnectionService;
use IdQueue\IdQueuePackagist\Utils\Helper;
use Illuminate\Http\Request;

class AuthServices
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->header('Authorization');
        if (empty($bearer)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authorization token is required!!',
            ]);
        }

        $jwt = str_replace('Bearer ', '', $bearer);
        if (empty($jwt)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Auth token is invalid!',
            ]);
        }

        $jwtData = Helper::isJwtValid($jwt);

        if (empty($jwtData['check']) || empty($jwtData['details'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Auth token is invalid!',
            ]);
        }

        if (! $jwtData['check']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired!',
            ]);
        }

        $user = json_decode($jwtData['details']);
        if (empty($user->Company_DB) || empty($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Auth token is invalid!',
            ]);
        }

        ConnectionService::setConnection($user);
        $request->merge((array) json_decode($jwtData['details']));
        auth()->loginUsingId($user->id);

        return $next($request);
    }
}
