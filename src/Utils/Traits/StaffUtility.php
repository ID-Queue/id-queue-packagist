<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use DB;

trait StaffUtility
{
    public static function checkIfStaffOnlineInBlding(string $servBuild, string $servtypes, int $dept_ID): bool
    {
        $servtype = self::return_ServiceNameByGUID($dept_ID, $servtypes);

        return DB::table('User_Accounts AS ua')
            ->join('Dispatch_Staff AS ds', 'ds.Acc_ID', '=', 'ua.id')
            ->where(function ($q) use ($servBuild) {
                $q->where('ua.Staff_Login_Location', $servBuild)
                    ->orWhere('ua.Staff_Login_Location', 'All');
            })
            ->where('ua.Staff_Login_State', 1)
            ->where('ds.Service', $servtype)
            ->where('ua.Company_Dept_ID', $dept_ID)
            ->exists();
    }
}
