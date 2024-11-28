<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class ServiceHour extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = false;

    // Disable auto-incrementing as Dispatch_Service_ID is a GUID

    // Disable timestamps as the table does not have created_at and updated_at columns
    protected $table = 'Service_Hours';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'Dispatch_Service_ID',
        'Company_Dept_ID',
        'Enable_After_Hours',
        'Sun_Open_Time',
        'Sun_Close_Time',
        'Mon_Open_Time',
        'Mon_Close_Time',
        'Tues_Open_Time',
        'Tues_Close_Time',
        'Wed_Open_Time',
        'Wed_Close_Time',
        'Thur_Open_Time',
        'Thur_Close_Time',
        'Fri_Open_Time',
        'Fri_Close_Time',
        'Sat_Open_Time',
        'Sat_Close_Time',
        'Msg_If_Off_Hours',
        'Building_GUID',
        'Master_On_Off',
        'Msg_Master_On_Off',
    ];
}
