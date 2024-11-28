<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    use CompanyDbConnection;

    // Specify the table name

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'User_guid', 'GUID');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
