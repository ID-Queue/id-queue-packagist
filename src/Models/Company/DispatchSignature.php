<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class DispatchSignature extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = true;

    // Primary key for the table

    // Disable auto-incrementing of the primary key (if necessary)
    protected $table = 'Dispatch_Signatures'; // or false, if your ID is not auto-incremented

    // Disable timestamps if your table does not have 'created_at' and 'updated_at' columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'ID',
        'Company_Dept_ID',
        'signator',
        'signature',
        'sig_hash',
        'Date_Create',
    ];

    // Optionally, you can add custom methods if necessary
}
