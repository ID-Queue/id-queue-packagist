<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    // Table associated with the model

    // Disable timestamps as the table does not have created_at and updated_at columns
    protected $table = 'Service_Logs';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'Company_Dept_ID',
        'Staff_GUID',
        'Staff_Service_Status',
        'Staff_Service_Details',
        'Staff_Service_Action_Time',
    ];
}
