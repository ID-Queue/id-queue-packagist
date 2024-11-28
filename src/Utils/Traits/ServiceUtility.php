<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use DB;
use IdQueue\IdQueuePackagist\Models\Company\ServiceHour;

trait ServiceUtility
{
    public static function getServiceHourValue(int $srvID, int $bldID, int $dept_ID): array
    {
        $data = ServiceHour::where('Company_Dept_ID', $dept_ID)
            ->where('Building_GUID', $bldID)
            ->where('Dispatch_Service_ID', $srvID)
            ->pluck(
                'Enable_After_Hours', 'Sun_Open_Time', 'Sun_Close_Time',
                'Mon_Open_Time', 'Mon_Close_Time', 'Tues_Open_Time', 'Tues_Close_Time',
                'Wed_Open_Time', 'Wed_Close_Time', 'Thur_Open_Time', 'Thur_Close_Time',
                'Fri_Open_Time', 'Fri_Close_Time', 'Sat_Open_Time', 'Sat_Close_Time',
                'Msg_If_Off_Hours'
            )
            ->first();

        return $data ? $data->toArray() : [];
    }

    public static function return_ServiceNameByGUID(int $dept_ID, string $srvNm): ?string
    {
        return DB::table('Dispatch_Service')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Service_GUID', $srvNm)
            ->value('Service_Name');
    }

    public static function checkavailabilitytime($bookeddate, $selected_slot, $dept_ID, $servBuild): bool
    {
        // Extract start and end times from the selected slot
        [$start_fulltime, $end_fulltime] = explode('-', $selected_slot);

        $selected = date('Y-m-d', strtotime($bookeddate));
        $start_datetime = "$selected ".date('H:i:s', strtotime($start_fulltime));
        $end_datetime = "$selected ".date('H:i:s', strtotime($end_fulltime));

        // Check if any overlapping entries exist in the Dispatch_Chart table
        $data = DB::table('Dispatch_Chart')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('App_service', $servBuild)
            ->where('Req_Time_Start', '<=', $start_datetime)
            ->where('Req_Time_Close', '>=', $start_datetime)
            ->whereNull('Staff_GUID')
            ->whereNull('Done_Time')
            ->whereNull('App_Declined')
            ->count();

        return $data === 0;
    }

    public static function return_VisitTypePriority($vtID, $dept_ID)
    {
        return DB::table('Dispatch_Visit_Type')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('ID', $vtID)
            ->value('Visit_Type_Priority'); // Use `value` instead of `pluck()->first()`.
    }

    public static function return_VisitTypeByID($vtID, $dept_ID)
    {
        return DB::table('Dispatch_Visit_Type')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('ID', $vtID)
            ->value('name'); // Use `value` to fetch the single column value directly.
    }

    public static function return_TotalPreschedTime($dept_ID): float|int
    {
        $data = DB::table('Admin_Service_Settings')
            ->where('Company_Dept_ID', $dept_ID)
            ->first(['Pre_Schedual_Metric', 'Pre_Schedual_Num']); // Use `first` for a single result instead of `get`

        if ($data) {
            return $data->Pre_Schedual_Metric * $data->Pre_Schedual_Num; // Direct access to properties
        }

        return 0; // Return 0 if no data is found
    }

    public static function insertIntoActiveQueue($dispID, $dept_ID): void
    {
        $data = DB::table('Dispatch_Chart')
            ->where('ID', $dispID)
            ->where('Company_Dept_ID', $dept_ID)
            ->first(); // Fetch the first matching record

        if ($data) {
            // Convert the object to an array and insert
            DB::table('Dispatch_Chart_Active_Queue')->insert((array) $data);
        }
    }

    public static function dispatchDeleteReason($dept_ID)
    {
        $data = DB::table('Dispatch_Delete_Reason')
            ->where('Company_Dept_ID', $dept_ID)
            ->orderBy('name', 'ASC')
            ->select(['name as label', 'name as value'])
            ->get();

        return $data->isNotEmpty() ? $data : null;
    }
}
