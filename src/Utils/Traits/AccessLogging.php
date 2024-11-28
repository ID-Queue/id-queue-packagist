<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use IdQueue\IdQueuePackagist\Models\Admin\AllAccessIp;
use IdQueue\IdQueuePackagist\Models\Company\AllowedAutoReqLocation;
use IdQueue\IdQueuePackagist\Models\Logs\PortalAccessLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

trait AccessLogging
{
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
