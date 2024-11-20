<?php

namespace IdQueue\IdQueuePackagist\Models\Logs;

use IdQueue\IdQueuePackagist\Traits\LogsDbConnection;
use Illuminate\Database\Eloquent\Model;

class PortalAccessLog extends Model
{
    use LogsDbConnection;

    // Define the table name if it does not follow the Laravel convention
    protected $table = 'Portal_Access_Logs';

    // Define the primary key (optional, as Laravel assumes 'id')
    protected $primaryKey = 'ID'; // You can specify this if your primary key is different

    // Disable automatic timestamps if your table does not have them
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'Event_DateTime',
        'IP_Address',
        'Event_Status',
        'Portal_Username',
        'Portal_Company_Code',
    ];

    // Optionally, you can define the types of columns in the table
    protected $casts = [
        'Event_DateTime' => 'datetime',
    ];
}
