<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DispatchBuilding extends Model
{
    use CompanyDbConnection;

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
}
