<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class CronReport extends Model
{
    // Table associated with the model
    public $incrementing = false;

    // Primary key for the table
    public $timestamps = false;

    // Disable auto-incrementing of the primary key (since the ID is not auto-incremented in your table)
    protected $table = 'Cron_Report';

    // Define the data types of the columns
    protected $primaryKey = 'ID';

    // Set the default values for attributes (like File_count)
    protected $casts = [
        'User_GUID' => 'uuid',
        'Request_created_time' => 'datetime',
        'File_created_time' => 'datetime',
    ];

    // Specify which attributes are mass assignable
    protected $attributes = [
        'File_count' => 0,
    ];

    // Disable timestamps if not needed
    protected $fillable = [
        'User_GUID',
        'Username',
        'Company_Dept_ID',
        'Search_filter',
        'Status',
        'Request_created_time',
        'File_created_time',
        'Random_key',
        'Report_type',
        'Role_type',
        'User_email',
        'File_count',
    ];
}
