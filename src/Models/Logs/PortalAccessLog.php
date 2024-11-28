<?php

namespace IdQueue\IdQueuePackagist\Models\Logs;

use IdQueue\IdQueuePackagist\Traits\LogsDbConnection; // Ensure the trait is correctly defined
use Illuminate\Database\Eloquent\Model; // The base Eloquent Model

class PortalAccessLog extends Model
{
    use LogsDbConnection; // Make sure the trait exists and is being used properly

    // Define the table name if it does not follow the Laravel convention
    protected $table = 'Portal_Access_Logs'; // Ensure this matches the table name in your DB

    // Define the primary key (optional, as Laravel assumes 'id' by default)
    protected $primaryKey = 'ID'; // Ensure this matches the actual primary key column in your DB

    // Disable automatic timestamps if your table does not have them
    public $timestamps = false; // Set this to false if your table doesn't have 'created_at' and 'updated_at'

    // Define the fillable attributes to protect against mass assignment
    protected $fillable = [
        'Event_DateTime',
        'IP_Address',
        'Event_Status',
        'Portal_Username',
        'Portal_Company_Code',
    ];

    // Optionally, you can define the types of columns in the table
    protected $casts = [
        'Event_DateTime' => 'datetime', // This ensures the 'Event_DateTime' column is treated as a datetime object
    ];
}
