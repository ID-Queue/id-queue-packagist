<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use DB;
use IdQueue\IdQueuePackagist\Models\Company\DispatchChartDetails;
use IdQueue\IdQueuePackagist\Models\Company\ServiceHour;

trait RequestUtility
{
    public static function checkIfRequestAlreadyExist(
        string $patNum,
        string $servReq,
        string $servBuild,
        string $loc,
        string $servtype,
        string $snglReqStr,
        int $dept_ID
    ): array {
        $columns = [
            'PID' => 'Pat_MRN',
            'Service' => 'App_Service',
            'Building' => 'App_Building_GUID',
            'Location' => 'App_Location_GUID',
            'Visit Type' => 'App_Visit_Type',
        ];

        if (empty($snglReqStr) || ! isset($columns[$snglReqStr])) {
            return response([
                'status' => 'error',
                'message' => 'Something Went Wrong!',
                'exception' => 'snglReqStr is Null or Invalid',
            ], 401);
        }

        $colVal = $columns[$snglReqStr];
        $findVal = $$snglReqStr;

        $exists = DB::table('Dispatch_Chart_Active_Queue')
            ->whereNull('App_Done')
            ->whereNull('App_Declined')
            ->where($colVal, $findVal)
            ->where('Company_Dept_ID', $dept_ID)
            ->exists();

        return $exists ? [1, $findVal] : [0, ''];
    }

    public static function disableRequestIfAfterHours(string $dateReqVal, int $dept_ID, string $srv_ID, string $bld_ID): array
    {
        $rows = ServiceHour::where('Company_Dept_ID', $dept_ID)
            ->where('Dispatch_Service_ID', $srv_ID)
            ->where('Building_GUID', $bld_ID)
            ->get(['Enable_After_Hours', 'Msg_If_Off_Hours', 'Master_On_Off', 'Msg_Master_On_Off']);

        foreach ($rows as $value) {
            if ($value->Master_On_Off == 1) {
                if (empty($value->Enable_After_Hours)) {
                    return [0, ''];
                }

                if (self::checkIfBetweenOC($dateReqVal, $dept_ID, $srv_ID, $bld_ID)) {
                    return [0, ''];
                }

                return [1, $value->Msg_If_Off_Hours];
            }

            return [2, $value->Msg_Master_On_Off ?? 'This request cannot be processed at this time. Please try again later.'];
        }

        return [2, 'Service Hour information unavailable'];
    }

    public function return_SQLCountZero($sqlStr)
    {
        return $sqlStr ? $sqlStr->count() : 0;
    }

    public static function injectRequestDetail($dept_ID, $idVal, $actionNum, $dateVal, $who): void
    {
        $values = [
            'Company_Dept_ID' => $dept_ID,
            'name' => $who,
            'Action_Time' => $dateVal,
            'Action_Taken' => $actionNum,
            'Request_ID' => $idVal,
        ];

        // Use insertOrIgnore to avoid duplicate errors (if applicable)
        DispatchChartDetails::create($values);
    }

    public static function customPagination($object, $request): array
    {
        $page = $request->page;
        $per_page = $request->per_page;

        $records_count = count($object);
        if ($records_count > 0) {
            $skip = $per_page * ($page - 1);
            $total_pages = ceil($records_count / $per_page);

            $record = array_slice($object, $skip, $per_page);

            return [
                'status' => $total_pages >= $page ? 'success' : 'error',
                'data' => $record,
                'count' => $records_count,
                'current_page' => $page,
                'total_pages' => $total_pages,
                'message' => $total_pages >= $page ? null : 'Record Not Found',
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Record Not Found',
        ];
    }
}
