<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class DispatchVisitType extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    // Table associated with the model
    public $incrementing = true;

    // Primary key for the table

    // Disable auto-incrementing of the primary key (if necessary)
    protected $table = 'Dispatch_Visit_Type';

    // Disable timestamps since the table does not have created_at and updated_at columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'ID',
        'Company_Dept_ID',
        'name',
        'time_complete',
        'Visit_Type_Enabled',
        'Visit_Type_Priority',
        'first_location',
        'second_location',
        'third_location',
    ];
}
