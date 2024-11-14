<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    // Specify the table name
    public $timestamps = false;

    // Specify the primary key if it's not the default 'id'
    protected $table = 'Admin_logs';

    // Disable timestamps since 'action_datetime' is manually handled
    protected $primaryKey = 'ID';

    // Define the attributes that can be mass assigned
    protected $fillable = [
        'Company_Dept_ID',
        'User_guid',
        'message',
        'action_datetime',
        'IP_address',
    ];
}
