<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DispatchZone extends Model
{
    use CompanyDbConnection;

    public $incrementing = false;

    protected $table = 'Dispatch_Zone';

    protected $primaryKey = 'Zone_GUID';

    protected $fillable = [
        'Zone_GUID',
        'Company_Dept_ID',
        'Building_ID',
        'name',
        'Zone_Enabled',
    ];

    /**
     * Define the relationship to DispatchBuilding.
     */
    public function dispatchBuilding(): BelongsTo
    {
        return $this->belongsTo(DispatchBuilding::class, 'Building_ID', 'Building_ID');
    }

    /**
     * Define the relationship to StaffStation.
     */
    public function staffStation(): HasOne
    {
        return $this->hasOne(StaffStation::class, 'Zone_GUID', 'Zone_GUID');
    }
}
