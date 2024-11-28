<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronReport extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = false;

    // Primary key for the table

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'User_GUID', 'GUID');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
