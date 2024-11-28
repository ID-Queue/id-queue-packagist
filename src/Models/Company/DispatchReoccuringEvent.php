<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchReoccuringEvent extends Model
{
    use CompanyDbConnection;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'Dispatch_Reoccuring_Events';

    protected $primaryKey = 'GUID';

    protected $fillable = [
        'ID',
        'GUID',
        'Company_Dept_ID',
        'App_Time',
        'App_Service',
        'App_Service_GUID',
        'App_LocDetail',
        'App_Location_GUID',
        'App_Zone_GUID',
        'App_Building_GUID',
        'Req_Time',
        'Notes',
        'Priority',
        'Who_Is_name',
        'App_Visit_Type',
        'App_Visit_Type_ID',
        'Pat_MRN',
        'Pat_Sec_ID',
        'Gender_Pref',
        'Req_EMail',
        'Event_Repeat',
        'Event_Start_Time',
        'Event_End_Time',
        'Event_Day_Sun',
        'Event_Day_Mon',
        'Event_Day_Tue',
        'Event_Day_Wed',
        'Event_Day_Thu',
        'Event_Day_Fri',
        'Event_Day_Sat',
        'Event_Day_Repeat',
        'Event_Day_Num',
        'Event_Week_Repeat',
        'Event_Week_Of_Year',
        'Event_Month_Jan',
        'Event_Month_Feb',
        'Event_Month_Mar',
        'Event_Month_Apr',
        'Event_Month_May',
        'Event_Month_Jun',
        'Event_Month_Jul',
        'Event_Month_Aug',
        'Event_Month_Sep',
        'Event_Month_Oct',
        'Event_Month_Nov',
        'Event_Month_Dec',
        'Event_Month_Repeat',
        'Event_Yearly_Repeat',
        'Series_Enabled',
        'Req_DoximityNo',
    ];

    protected $casts = [
        'App_Time' => 'datetime',
        'Req_Time' => 'datetime',
        'Event_Start_Time' => 'datetime',
        'Event_End_Time' => 'datetime',
        'Event_Day_Sun' => 'boolean',
        'Event_Day_Mon' => 'boolean',
        'Event_Day_Tue' => 'boolean',
        'Event_Day_Wed' => 'boolean',
        'Event_Day_Thu' => 'boolean',
        'Event_Day_Fri' => 'boolean',
        'Event_Day_Sat' => 'boolean',
        'Event_Month_Jan' => 'boolean',
        'Event_Month_Feb' => 'boolean',
        'Event_Month_Mar' => 'boolean',
        'Event_Month_Apr' => 'boolean',
        'Event_Month_May' => 'boolean',
        'Event_Month_Jun' => 'boolean',
        'Event_Month_Jul' => 'boolean',
        'Event_Month_Aug' => 'boolean',
        'Event_Month_Sep' => 'boolean',
        'Event_Month_Oct' => 'boolean',
        'Event_Month_Nov' => 'boolean',
        'Event_Month_Dec' => 'boolean',
        'Series_Enabled' => 'boolean',
        'Priority' => 'integer',
        'App_Visit_Type_ID' => 'integer',
        'Pat_Sec_ID' => 'integer',
        'Req_DoximityNo' => 'integer',
        'Event_Day_Repeat' => 'integer',
        'Event_Day_Num' => 'integer',
        'Event_Week_Repeat' => 'integer',
        'Event_Week_Of_Year' => 'integer',
        'Event_Month_Repeat' => 'integer',
        'Event_Yearly_Repeat' => 'integer',
        'Company_Dept_ID' => 'integer',
    ];

    protected $guarded = ['GUID'];

    /**
     * Define the relationship to CompanyDepartment.
     */
    public function companyDepartment(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    /**
     * Define the relationship to AppService.
     */
    public function appService(): BelongsTo
    {
        return $this->belongsTo(DispatchService::class, 'App_Service_GUID', 'GUID');
    }

    /**
     * Define the relationship to DispatchZone.
     */
    public function dispatchZone(): BelongsTo
    {
        return $this->belongsTo(DispatchZone::class, 'App_Zone_GUID', 'Zone_GUID');
    }

    /**
     * Define the relationship to DispatchBuilding.
     */
    public function dispatchBuilding(): BelongsTo
    {
        return $this->belongsTo(DispatchBuilding::class, 'App_Building_GUID', 'Building_GUID');
    }

    /**
     * Define the relationship to StaffStation.
     */
    public function staffStation(): BelongsTo
    {
        return $this->belongsTo(StaffStation::class, 'App_Location_GUID', 'Location_GUID');
    }

    //    /**
    //     * Define the relationship to Patient.
    //     */
    //    public function patient(): BelongsTo
    //    {
    //        return $this->belongsTo(Patient::class, 'Pat_MRN', 'MRN');
    //    }

    /**
     * Define the relationship to EventVisitType.
     */
    public function eventVisitType(): BelongsTo
    {
        return $this->belongsTo(DispatchVisitType::class, 'App_Visit_Type_ID', 'Visit_Type_ID');
    }
}
