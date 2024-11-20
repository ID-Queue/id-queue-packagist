<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminStaff extends Model
{
    use CompanyDbConnection;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'Admin_Staff';

    protected $primaryKey = 'Acc_ID';

    protected $fillable = [
        'Company_Dept_ID',
        'Service',
        'Acc_ID',
    ];

    /**
     * Define the relationship to UserAccount.
     */
    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(User::class, 'Acc_ID', 'Acc_ID');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
