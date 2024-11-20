<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DispatchLocation extends Model
{
    use CompanyDbConnection;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'Dispatch_Location';

    protected $primaryKey = 'Location_GUID';

    protected $fillable = [
        'Location_GUID',
        'Company_Dept_ID',
        'Zone_ID',
        'name',
        'Location_Enabled',
    ];

    /**
     * Define the relationship to DispatchZone.
     */
    public function dispatchZone(): BelongsTo
    {
        return $this->belongsTo(DispatchZone::class, 'Zone_ID', 'Zone_ID');
    }

    /**
     * Define the relationship to StaffStation.
     */
    public function staffStation(): HasOne
    {
        return $this->hasOne(StaffStation::class, 'Location_GUID', 'Location_GUID');
    }
}
