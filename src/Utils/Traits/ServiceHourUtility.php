<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use DB;
use IdQueue\IdQueuePackagist\Models\Company\ServiceHour;

trait ServiceHourUtility
{
    public static function checkIfBetweenOC(string $dateReqVal, int $dept_ID, string $srv_ID, string $bld_ID): bool
    {
        $dayOfWeekMap = [
            0 => ['Sun_Open_Time', 'Sun_Close_Time'],
            1 => ['Mon_Open_Time', 'Mon_Close_Time'],
            2 => ['Tues_Open_Time', 'Tues_Close_Time'],
            3 => ['Wed_Open_Time', 'Wed_Close_Time'],
            4 => ['Thur_Open_Time', 'Thur_Close_Time'],
            5 => ['Fri_Open_Time', 'Fri_Close_Time'],
            6 => ['Sat_Open_Time', 'Sat_Close_Time'],
        ];

        $dayOfWeek = date('w', strtotime($dateReqVal));
        [$openCol, $closeCol] = $dayOfWeekMap[$dayOfWeek];

        [$openTime, $closeTime] = self::getOpenCloseHours($openCol, $closeCol, $dept_ID, $srv_ID, $bld_ID);

        $currentTime = date('H:i');
        $openTime = $openTime ? date('H:i', strtotime($openTime)) : '00:01';
        $closeTime = $closeTime ? date('H:i', strtotime($closeTime)) : '23:59';

        return $currentTime >= $openTime && $currentTime <= $closeTime;
    }

    public static function getOpenCloseHours(
        string $openCol,
        string $closeCol,
        int $dept_ID,
        string $srv_ID,
        string $bld_ID
    ): array {
        $data = ServiceHour::where('Company_Dept_ID', $dept_ID)
            ->where('Dispatch_Service_ID', $srv_ID)
            ->where('Building_GUID', $bld_ID)
            ->first([$openCol, $closeCol]);

        return [$data->$openCol ?? null, $data->$closeCol ?? null];
    }

    // In ServiceHourUtility trait
    public function return_AfterHoursMessage($dept_ID, $srv_ID, $bld_ID)
    {
        return DB::table('Service_Hours')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Dispatch_Service_ID', $srv_ID)
            ->where('Building_GUID', $bld_ID)
            ->value('Msg_If_Off_Hours');
    }

    public static function return_reqTime($reqStart, $reqComp): string
    {
        $strTime = new DateTime($reqStart);
        $endTime = new DateTime($reqComp);
        $delta_T = $strTime->diff($endTime);

        $totTime = ($delta_T->h * 3600) + ($delta_T->i * 60) + $delta_T->s;

        $newHour = floor($totTime / 3600);
        $newMin = floor(($totTime % 3600) / 60);
        $newSec = $totTime % 60;

        // Format time in HH:MM:SS
        return ($newHour > 0 ? str_pad($newHour, 2, '0', STR_PAD_LEFT).':' : '00:')
            .str_pad($newMin, 2, '0', STR_PAD_LEFT).':'
            .str_pad($newSec, 2, '0', STR_PAD_LEFT);
    }
}
