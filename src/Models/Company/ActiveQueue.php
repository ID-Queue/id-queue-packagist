<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActiveQueue extends Model
{
    use CompanyDbConnection;

    // Specify the table name since it doesn't follow the default plural naming convention

    protected $table = 'Dispatch_Chart_Active_Queue';

    // Set the primary key if it's not the default 'id'

    // Set the key type to string (UUID is typically a string in Laravel)
    protected $primaryKey = 'GUID';

    // Disable automatic timestamp management if your table doesn't have 'created_at' and 'updated_at'
    protected $keyType = 'string';

    // Set the columns that are mass assignable
    protected $fillable = [
        'GUID',
        'Reoccuring_GUID',
        'Company_Dept_ID',
        'App_Time',
        'App_Service',
        'App_Service_GUID',
        'App_Location_GUID',
        'App_Pre_Schedual_Time',
        'App_Zone_GUID',
        'App_Building_GUID',
        'Staff_GUID',
        'Req_Time',
        'App_Visit_Type',
        'App_Visit_Type_ID',
        'Priority',
        'App_Paused',
        'Pat_MRN',
        'Paused_Time',
        'App_Approved',
        'Final_Notes',
        'Approved_Time',
        'App_Session',
        'Session_Time',
        'Pat_ID_FN',
        'Pat_Sec_ID',
        'Who_Is_Name',
        'Pat_ID_LN',
        'App_Arrived',
        'Arrived_Time',
        'App_Done',
        'Done_Time',
        'App_Declined',
        'Declined_Time',
        'Dispatched_Time',
        'Deleted_Reason',
        'Notes',
        'App_LocDetail',
        'Gender_Pref',
        'Who_Is_Ext',
        'Req_DoximityNo',
        'Release_Notes',
        'Req_Video_Conf',
        'Deleted_By_Name',
        'Req_EMail',
        'Pre_Req_Time',
        'Req_Time_Close',
        'Req_Time_Start',
        'Dispatcher_GUID',
        'Dispatch_Notes',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    // Define relationships to other models
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
}
