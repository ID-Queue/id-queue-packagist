<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class DispatchVideoReq extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = true;

    // Primary key for the table
    public $timestamps = false;

    // Disable auto-incrementing of the primary key (if necessary)
    protected $table = 'Dispatch_Video_Req'; // or false, if your ID is not auto-incremented

    // Disable timestamps if your table does not have 'created_at' and 'updated_at' columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'ID',
        'Room_ID',
        'Company_Dept_ID',
        'Req_ID',
        'Req_DT',
        'Req_Comp_DT',
        'Room_Password',
        'Enable_Video_Room',
    ];

    // Optionally, you can add custom methods if necessary
}
