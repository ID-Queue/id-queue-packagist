<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class iOSDeviceToken extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = false;

    // Primary key for the table
    public $timestamps = false;

    // Disable auto-incrementing as 'ID' is not an auto-incrementing column
    protected $table = 'iOS_Device_Tokens';

    // Disable timestamps as the table does not have created_at and updated_at columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'Company_Dept_ID',
        'Token',
        'ID',
        'status',
        'verify_code',
        'device_name',
    ];

    // Optionally, you can add relationships if this model needs to interact with other models.
}
