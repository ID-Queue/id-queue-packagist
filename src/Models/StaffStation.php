<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class StaffStation extends Model
{
    public $timestamps = false;

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

    // Specify the database connection if needed
    protected $connection = 'db_connection'; // Disabling timestamps if not present in the table
}
