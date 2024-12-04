<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeleteReason extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    public $incrementing = false;

    protected $table = 'Dispatch_Delete_Reason';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID',
        'Company_Dept_ID',
        'name',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
