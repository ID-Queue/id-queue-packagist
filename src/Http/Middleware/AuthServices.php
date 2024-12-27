<?php

namespace IdQueue\IdQueuePackagist\Http\Middleware;

use Closure;
use IdQueue\IdQueuePackagist\Enums\AppSettings;
use IdQueue\IdQueuePackagist\Models\Company\AdminServiceSetting;
use IdQueue\IdQueuePackagist\Services\ConnectionService;
use IdQueue\IdQueuePackagist\Utils\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthServices
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Retrieve the Authorization header
            $bearer = $request->header('Authorization');

            if (empty($bearer)) {
                throw new Exception('Authorization token is required!');
            }

            // Extract JWT from Bearer token
            $jwt = str_replace('Bearer ', '', $bearer);

            if (empty($jwt)) {
                throw new Exception('Auth token is invalid!');
            }

            // Validate the JWT token
            $jwtData = Helper::isJwtValid($jwt);
            if (empty($jwtData['check']) || empty($jwtData['details'])) {
                throw new Exception('Token has expired or is invalid!');
            }

            $user = json_decode($jwtData['details']);

            // Ensure necessary user details are present
            if (empty($user->Company_DB) || empty($user->id)) {
                throw new Exception('Auth token is invalid!');
            }

            // Establish database connection for the user
            ConnectionService::setConnection($user);

            // Retrieve and set the application's timezone
            $timeZone = AdminServiceSetting::getSettingFor(AppSettings::Default_Time_Zone);

            if (!$timeZone) {
                throw new Exception('Unable to fetch the default timezone setting.');
            }

            config(['app.timezone' => $timeZone]);
            date_default_timezone_set($timeZone);

            // Merge user details into the request object and authenticate
            $request->merge((array)$user);
            Auth::loginUsingId($user->id);

            return $next($request);
        } catch (Exception $e) {
            // Handle exceptions and return a JSON error response
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Return a standardized error response.
     *
     * @param string $message
     * @return JsonResponse
     */
    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 400);
    }
}
