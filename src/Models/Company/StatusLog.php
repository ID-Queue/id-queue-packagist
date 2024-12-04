<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    // Specify the table name since it doesn't follow the default plural naming convention

    // Set the primary key if it's not the default 'id'
    protected $table = 'User_Status_Logs';

    protected $primaryKey = 'Staff_GUID'; // Specify the database connection if needed

    // Disable automatic timestamp management if your table doesn't have 'created_at' and 'updated_at'

    // Set the columns that are mass assignable
    protected $fillable = [
        'Staff_GUID',
        'Company_Dept_ID',
        'Staff_Status',
        'Staff_Detail',
        'Staff_Action_DateTime',
    ];

    // Define the relationship with UserAccount
    public function userAccount()
    {
        return $this->belongsTo(User::class, 'Staff_GUID');
    }
}
