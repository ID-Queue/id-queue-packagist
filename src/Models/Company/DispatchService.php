<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchService extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false


    public $incrementing = false;

    protected $table = 'Dispatch_Service';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID',
        'Company_Dept_ID',
        'Service_GUID',
        'Service_Name',
        'Service_Abrv',
        'cost_val',
        'time_interval',
        'Service_Qty',
    ];

    protected $casts = [
        'cost_val' => 'decimal:2',
    ];

    /**
     * Define the relationship to ActiveQueue.
     */
    public function activeQueue(): BelongsTo
    {
        return $this->belongsTo(ActiveQueue::class, 'App_Service');
    }
}
