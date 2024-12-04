<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispatchChart extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    use HasUuids;

    public $incrementing = false;

    protected $table = 'Dispatch_Chart';

    protected $primaryKey = 'GUID';

    protected $fillable = [
        'ID',
        'GUID',
        'Reoccuring_GUID',
        'Company_Dept_ID',
        'App_Time',
        'App_Service',
        'App_Service_GUID',
        'App_Location_GUID',
        'App_LocDetail',
        'App_Pre_Schedual_Time',
        'App_Zone_GUID',
        'App_Building_GUID',
        'Staff_GUID',
        'Req_DoximityNo',
        'Priority',
        'App_Visit_Type_ID',
        'Pat_MRN',
        'Pat_ID_FN',
        'Pat_ID_LN',
        'Gender_Pref',
        'Req_Video_Conf',
        'App_Visit_Type',
        'Deleted_by_Name',
        'Deleted_Reason',
        'Req_EMail',
        'Who_Is_Name',
        'Who_Is_Ext',
        'Final_Notes',
        'Notes',
        'Approved_Time',
        'Pre_Req_Time',
        'Req_Time_Close',
        'Req_Time_Start',
        'Req_Time',
        'Arrived_Time',
        'Pat_Sec_ID',
        'Session_Time',
        'Done_Time',
        'Declined_Time',
        'Dispatched_Time',
    ];

    protected $casts = [
        'Req_Video_Conf' => 'boolean',
        'App_Time' => 'datetime',
        'Approved_Time' => 'datetime',
        'Arrived_Time' => 'datetime',
        'Session_Time' => 'datetime',
        'Done_Time' => 'datetime',
        'Declined_Time' => 'datetime',
        'Dispatched_Time' => 'datetime',
        'Company_Dept_ID' => 'integer',
        'Priority' => 'integer',
        'App_Visit_Type_ID' => 'integer',
        'Req_DoximityNo' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    public function dispatchLocation(): BelongsTo
    {
        return $this->belongsTo(DispatchLocation::class, 'App_Location_GUID');
    }

    public function dispatchBuilding(): BelongsTo
    {
        return $this->belongsTo(DispatchBuilding::class, 'App_Building_GUID');
    }

    public function dispatchZone(): BelongsTo
    {
        return $this->belongsTo(DispatchZone::class, 'App_Zone_GUID');
    }

    public function appService(): BelongsTo
    {
        return $this->belongsTo(DispatchService::class, 'App_Service_GUID');
    }

    public function dispatchService(): HasMany
    {
        return $this->hasMany(DispatchService::class, 'Service_Name');
    }

    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(User::class, 'Staff_GUID', 'GUID');
    }

    public function visitType(): BelongsTo
    {
        return $this->belongsTo(DispatchVisitType::class, 'App_Visit_Type_ID', 'ID');
    }
}
