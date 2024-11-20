<?php

namespace IdQueue\IdQueuePackagist\Models\Admin;

use IdQueue\IdQueuePackagist\Traits\AdminDbConnection;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use AdminDbConnection;

    protected $table = 'Admin_logs';

    protected $primaryKey = 'ID';

    public $timestamps = false;  // Disable timestamps if they are not used

    protected $fillable = [
        'Company_Dept_ID',
        'User_guid',
        'message',
        'action_datetime',
    ];
}
