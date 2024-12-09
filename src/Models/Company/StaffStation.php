<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StaffStation extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    protected $table = 'Staff_Station';

    protected $fillable = [
        'Company_Dept_ID',
        'Staff_GUID',
        'App_Location_GUID',
        'App_Zone_GUID',
        'App_Building_GUID',
        'Station_Time',
        'stationed_status',
    ];

    public function location(): hasOne
    {
        return $this->hasOne(DispatchLocation::class, 'Location_GUID', 'App_Location_GUID');
    }

    public function zone(): hasOne
    {
        return $this->hasOne(DispatchZone::class, 'Zone_GUID', 'App_Zone_GUID');
    }

    public function building(): hasOne
    {
        return $this->hasOne(DispatchBuilding::class, 'Building_GUID', 'App_Building_GUID');
    }

    // Specify the database connection if needed
    // Disabling timestamps if not present in the table
}
