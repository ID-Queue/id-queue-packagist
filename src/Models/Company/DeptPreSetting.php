<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeptPreSetting extends Model
{
    use CompanyDbConnection;

    // Specify the table name if it differs from the plural of the model name
    public $incrementing = true;

    // Define the primary key
    protected $table = 'Dept_Pre_Settings';

    protected $primaryKey = 'ID';

    // If the primary key is not auto-incrementing (set to true if it's an auto-incrementing integer)
    // Change to false if it's a UUID or non-incrementing

    // Define the key type (set to 'int' or 'string' as per your primary key type)
    protected $keyType = 'int';

    // Define the fillable fields
    protected $fillable = [
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
        'Account_Status' => 'boolean', // Assuming this is a bit field, can be cast to boolean
        'Visit_Type_Single' => 'string',
        'Requester_ID' => 'string',
        'Second_Person_ID' => 'string',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
