<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LastLocation extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    public $incrementing = false;  // No timestamps in the table

    protected $table = 'Last_Location'; // No primary key defined

    protected $primaryKey = null; // Assumes the table doesn't use auto-incrementing primary key

    protected $fillable = [
        'Company_Dept_ID',
        'Staff_GUID',
        'App_Location_GUID',
        'App_Zone_GUID',
        'App_Building_GUID',
        'Location_Time',
    ];

    protected $casts = [
        'Company_Dept_ID' => 'integer',
        'Staff_GUID' => 'string',
        'App_Location_GUID' => 'string',
        'App_Zone_GUID' => 'string',
        'App_Building_GUID' => 'string',
        'Location_Time' => 'string',
    ];

    public function location(): HasOne
    {
        return $this->hasOne(DispatchLocation::class, 'Location_GUID', 'App_Location_GUID');
    }

    public function zone(): HasOne
    {
        return $this->hasOne(DispatchZone::class, 'Zone_GUID', 'App_Zone_GUID');
    }

    public function building(): HasOne
    {
        return $this->hasOne(DispatchBuilding::class, 'Building_GUID', 'App_Building_GUID');
    }

    // You can define relationships here if necessary, e.g., belongsTo, hasMany, etc.
}
