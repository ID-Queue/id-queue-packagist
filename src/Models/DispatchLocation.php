<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchLocation extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'Dispatch_Location';

    protected $primaryKey = 'Location_GUID';

    protected $connection = 'db_connection';

    protected $fillable = [
        'Location_GUID',
        'Company_Dept_ID',
        'Zone_ID',
        'name',
        'Location_Enabled',
    ];

    /**
     * Define the relationship to DispatchZone.
     */
    public function dispatchZone()
    {
        return $this->belongsTo(DispatchZone::class, 'Zone_ID', 'Zone_ID');
    }

    /**
     * Define the relationship to StaffStation.
     */
    public function staffStation()
    {
        return $this->hasOne(StaffStation::class, 'Location_GUID', 'Location_GUID');
    }
}
