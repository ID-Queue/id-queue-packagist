<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DispatchBuilding extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    public $incrementing = false;

    protected $table = 'Dispatch_Building';

    protected $primaryKey = 'Building_GUID';

    protected $keyType = 'string';

    protected $fillable = [
        'Building_GUID',
        'name',
        'Company_Dept_ID',
        'Ext_Queue_Url',
        'Ext_Queue_Active',
        'Building_Enabled',
    ];

    // Define relationship with StaffStation
    public function staffStation(): HasOne
    {
        return $this->hasOne(StaffStation::class, 'Building_GUID', 'Building_GUID');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    /**
     * Get building names by department ID and building IDs.
     *
     * @param  string  $bldID  Comma-separated building IDs
     */
    public static function getBuildingsByID(int $dept_ID, string $bldID): array
    {
        // Convert comma-separated string to an array
        $buildingIDs = explode(',', $bldID);

        // Query the database using Eloquent
        return self::where('Company_Dept_ID', $dept_ID)
            ->whereIn('Building_GUID', $buildingIDs)
            ->pluck('name')
            ->toArray();
    }
}
