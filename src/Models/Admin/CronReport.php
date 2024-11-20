<?php

namespace IdQueue\IdQueuePackagist\Models\Admin;

use IdQueue\IdQueuePackagist\Traits\AdminDbConnection;
use Illuminate\Database\Eloquent\Model;

class CronReport extends Model
{
    use AdminDbConnection;

    protected $table = 'Cron_Report';

    protected $primaryKey = 'ID';

    public $timestamps = false;  // Disable timestamps if they are not used

    protected $fillable = [
        'Company_code',
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
        'adv_report_col',
    ];
}
