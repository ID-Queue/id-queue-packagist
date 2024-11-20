<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchChartDetails extends Model
{
    use CompanyDbConnection;

    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'Dispatch_Chart_Details';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'Company_Dept_ID',
        'name',
        'Action_Time',
        'Action_Taken',
        'Request_ID',
    ];

    protected $casts = [
        'Action_Time' => 'string',
        'Action_Taken' => 'integer',
        'Request_ID' => 'integer',
        'Company_Dept_ID' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    public function dispatchChart(): BelongsTo
    {
        return $this->belongsTo(DispatchChart::class, 'Request_ID', 'ID');
    }
}
