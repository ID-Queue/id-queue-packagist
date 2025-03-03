<?php

namespace IdQueue\IdQueuePackagist\Services;

use IdQueue\IdQueuePackagist\Models\Company\StatusLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffStatusLogService
{
    /**
     * Log a staff status event.
     */
    public function logStaffStatusEvent(int $companyDeptId, int $staffStatus, string $message): void
    {
        try {
            StatusLog::create([
                'Staff_GUID' => Auth::user()->GUID,
                'Company_Dept_ID' => $companyDeptId,
                'Staff_Status' => $staffStatus,
                'Staff_Detail' => $message,
                'Staff_Action_DateTime' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log staff status event: '.$e->getMessage());
        }
    }
}
