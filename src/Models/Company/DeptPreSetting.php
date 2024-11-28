<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeptPreSetting extends Model
{
    use CompanyDbConnection;

    // Specify the table name
    protected $table = 'Dept_Pre_Settings';

    // Define the primary key
    protected $primaryKey = 'ID';

    // Indicate if the primary key is auto-incrementing
    public $incrementing = true;

    // Define the key type
    protected $keyType = 'int';

    // Disable timestamps if not used

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'ID', // Include only if explicitly assigning IDs
        'Company_Dept_ID',
        'Company_Dept',
        'Service_Single',
        'Staff_Single',
        'Building_Single',
        'Zone_Single',
        'Location_Single',
        'Person_ID',
        'Univ_Req_Password',
        'Account_Status',
        'Visit_Type_Single',
        'Requester_ID',
        'Second_Person_ID',
    ];

    // Define attribute casting
    protected $casts = [
        'ID' => 'integer',
        'Company_Dept_ID' => 'integer',
        'Company_Dept' => 'string',
        'Service_Single' => 'string',
        'Staff_Single' => 'string',
        'Building_Single' => 'string',
        'Zone_Single' => 'string',
        'Location_Single' => 'string',
        'Person_ID' => 'string',
        'Univ_Req_Password' => 'string',
        'Account_Status' => 'boolean', // Assuming this is a bit field
        'Visit_Type_Single' => 'string',
        'Requester_ID' => 'string',
        'Second_Person_ID' => 'string',
    ];

    // Define the relationship with the DispatchDepartment model
    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
