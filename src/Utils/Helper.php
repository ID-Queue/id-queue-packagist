<?php

namespace IdQueue\IdQueuePackagist\Utils;

use Carbon\Carbon;
use Exception;
use IdQueue\IdQueuePackagist\Models\Admin\AllAccessIp;
use IdQueue\IdQueuePackagist\Models\Company\AllowedAutoReqLocation;
use IdQueue\IdQueuePackagist\Models\Company\DeptPreSetting;
use IdQueue\IdQueuePackagist\Models\Company\User;
use IdQueue\IdQueuePackagist\Models\Logs\PortalAccessLog;
use Illuminate\Support\Facades\Log;

class Helper
{
    /**
     * Validate a JWT token.
     */
    public static function isJwtValid(string $jwt, string $secret = 'secret'): array
    {
        try {
            $tokenParts = explode('.', $jwt);

            if (count($tokenParts) !== 3) {
                throw new Exception('Invalid JWT format.');
            }

            [$encodedHeader, $encodedPayload, $providedSignature] = $tokenParts;

            $header = json_decode(base64_decode($encodedHeader), true);
            $payload = json_decode(base64_decode($encodedPayload), true);

            if (! $header || ! $payload) {
                throw new Exception('Invalid base64 encoding in JWT.');
            }

            $expiration = $payload['exp'] ?? null;
            if (! $expiration) {
                throw new Exception('Expiration claim (exp) missing in JWT.');
            }

            $isTokenExpired = ($expiration - time()) < 0;

            $validSignature = self::base64UrlEncode(
                hash_hmac('SHA256', "$encodedHeader.$encodedPayload", $secret, true)
            );

            $isSignatureValid = ($providedSignature === $validSignature);

            return [
                'check' => ! $isTokenExpired && $isSignatureValid,
                'details' => $payload,
            ];
        } catch (Exception $e) {
            Log::error('JWT validation failed: '.$e->getMessage());

            return [
                'check' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Base64 URL-safe encoding.
     */
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Get the first and last name of a user by department ID and username.
     */
    public static function getUserFirstLastName(int $deptId, string $username): array|false
    {
        $user = User::where('Company_Dept_ID', $deptId)
            ->where('username', $username)
            ->select(['First_name', 'Last_name'])
            ->first();

        return $user ? [$user->First_name, $user->Last_name] : false;
    }

    /**
     * Get department values for a given department ID.
     */
    public static function getDeptValue(int $deptId): array
    {
        $data = DeptPreSetting::where('Company_Dept_ID', $deptId)
            ->select([
                'Company_Dept',
                'Service_Single',
                'Staff_Single',
                'Location_Single',
                'Zone_Single',
                'Building_Single',
                'Person_ID',
                'Second_Person_ID',
                'Requester_ID',
            ])
            ->first();

        return $data ? (array) $data : [];
    }

    /**
     * Log access events.
     */
    public static function logAccessEvent(string $ip, string $failedPortalAccount, string $unVal, string $ccVal): void
    {
        $data = [
            'Event_DateTime' => Carbon::now()->toDateTimeString(),
            'IP_Address' => $ip,
            'Event_Status' => $failedPortalAccount,
            'Portal_Username' => $unVal,
            'Portal_Company_Code' => $ccVal,
        ];

        PortalAccessLog::create($data);
        Log::channel('stderr')->error(json_encode($data));
    }

    /**
     * Check if a location is allowed based on IP.
     */
    public static function checkIfAllowLocation(string $originIPVal): bool
    {
        $ipsLong = array_map('ip2long', explode(',', $originIPVal));

        $allowedRanges = array_merge(
            AllowedAutoReqLocation::all(['Start_IP', 'End_IP'])->toArray(),
            AllAccessIp::all(['Start_IP', 'End_IP'])->toArray()
        );

        foreach ($allowedRanges as $range) {
            $startIP = ip2long($range['Start_IP']);
            $endIP = ip2long($range['End_IP']);

            foreach ($ipsLong as $ip) {
                if ($ip >= $startIP && $ip <= $endIP) {
                    return true;
                }
            }
        }

        return false;
    }
}
