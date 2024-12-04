<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class UserZone extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    public $incrementing = false;

    // Since there's no primary key

    protected $table = 'User_Zones'; // Disabling auto-incrementing since there is no primary key

    protected $primaryKey = null;

    protected $fillable = [
        'User_ID',
        'Zone_ID',
    ];

    // Specify the database connection if needed
    // Disabling timestamps if not present in the table
}
