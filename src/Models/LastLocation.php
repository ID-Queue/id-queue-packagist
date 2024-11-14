<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class LastLocation extends Model
{
    public $timestamps = false;

    public $incrementing = false;  // No timestamps in the table

    protected $table = 'Last_Location'; // No primary key defined

    protected $primaryKey = null; // Assumes the table doesn't use auto-incrementing primary key

    protected $connection = 'db_connection';

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

    // You can define relationships here if necessary, e.g., belongsTo, hasMany, etc.
}
