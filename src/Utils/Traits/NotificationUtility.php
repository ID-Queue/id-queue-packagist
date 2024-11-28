<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use DB;

trait NotificationUtility
{
    public static function pushIOSNotificationp8($servReqNm, $dept_ID, $db_name)
    {
        // Fetch active device tokens
        $tokenList = DB::table('iOS_Device_Tokens')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('status', 1)
            ->pluck('Token') // Directly fetch the tokens
            ->toArray();

        if (empty($tokenList)) {
            return; // No tokens found, exit early
        }

        $dateVal = now(); // Use Laravel's `now()` helper
        $preSchedTime = self::return_TotalPreschedTime($dept_ID);
        $preSchedTimeAdd = $dateVal->addMinutes($preSchedTime); // Use Carbon's addMinutes

        // Get active requests within the scheduled time frame
        $activeReq = DB::table('Dispatch_Chart_Active_Queue')
            ->where('Company_Dept_ID', $dept_ID)
            ->where(function ($q) {
                $q->whereNull('App_Declined')->orWhere('App_Declined', '<>', true);
            })
            ->where(function ($q) {
                $q->whereNull('App_Done')->orWhere('App_Done', '<>', true);
            })
            ->where(function ($q) use ($preSchedTimeAdd) {
                $q->whereNull('App_Pre_Schedual_Time')->orWhere('App_Pre_Schedual_Time', '<=', $preSchedTimeAdd);
            })
            ->whereNull('App_Approved')
            ->count(); // Count the active requests

        $message = "New Request for $servReqNm, Total Pending $activeReq FROM $db_name on ".$dateVal->format('m/d/Y H:i a');

        // Send notifications
        foreach ($tokenList as $token) {
            self::send_ios_notifications($token, $message);
        }
    }

    public static function pushbrowserNotification($servReqNm, $dept_ID): void
    {
        $dateVal = now(); // Use Laravel's `now()` for the current date and time
        $preSchedTime = self::return_TotalPreschedTime($dept_ID);
        $preSchedTimeAdd = $dateVal->addMinutes($preSchedTime); // Add scheduled time using Carbon

        $sqlActiveReq = DB::table('Dispatch_Chart_Active_Queue')
            ->where('Company_Dept_ID', $dept_ID)
            ->where(function ($q) {
                $q->where('App_Declined', '<>', true)
                    ->orWhereNull('App_Declined');
            })
            ->where(function ($q) {
                $q->where('App_Done', '<>', true)
                    ->orWhereNull('App_Done');
            })
            ->where(function ($q) use ($preSchedTimeAdd) {
                $q->where('App_Pre_Schedual_Time', '<=', $preSchedTimeAdd)
                    ->orWhereNull('App_Pre_Schedual_Time');
            })
            ->whereNull('App_Approved')
            ->get();

        $activeReq = self::return_SQLCountZero($sqlActiveReq);
        $activeReq += 1; // Increment active requests count
    }

    public static function send_ios_notifications($token_id, $message): void
    {
        $path = dirname(__FILE__, 2).'/p8files';
        $filename = date('YmdHis').'_bat.bat';
        $filePath = $path.'/'.$filename;

        $myfile = fopen($filePath, 'w') or exit('Unable to open file!');

        // Write the commands to the batch file
        $txt = 'cd '.$path."\n";
        fwrite($myfile, $txt);

        $txt = "bash apnsTest.sh -a 'com.id-queue.notifier-req2-ent' -e 'prod' -d '".$token_id."' -m '".$message."'\n";
        fwrite($myfile, $txt);

        $txt = "exit;\n";
        fwrite($myfile, $txt);

        fclose($myfile);

        // Execute the batch file
        shell_exec('start call '.$filePath);

        // Cleanup
        sleep(1); // Small delay to allow the process to complete before deletion
        unlink($filePath);
    }
}
