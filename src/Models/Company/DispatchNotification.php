<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchNotification extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    /**
     * Indicates if the model should use auto-incrementing IDs.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Dispatch_Notification';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ID',
        'Company_Dept_ID',
        'Enable_RT_Staff_Wrn',
        'Enable_RT_Contract_Wrn',
        'Enable_RT_PerDiem_Wrn',
        'Enable_PS_Staff_Wrn',
        'Enable_PS_Contract_Wrn',
        'Enable_PS_PerDiem_Wrn',
        'Enable_Notice_Submit',
        'Enable_Notice_Accept',
        'Enable_Notice_Arrived',
        'Enable_Notice_InSession',
        'Enable_Notice_Complete',
        'Enable_Notice_Delete',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ID' => 'integer',
        'Company_Dept_ID' => 'integer',
        'Enable_RT_Staff_Wrn' => 'boolean',
        'Enable_RT_Contract_Wrn' => 'boolean',
        'Enable_RT_PerDiem_Wrn' => 'boolean',
        'Enable_PS_Staff_Wrn' => 'boolean',
        'Enable_PS_Contract_Wrn' => 'boolean',
        'Enable_PS_PerDiem_Wrn' => 'boolean',
        'Enable_Notice_Submit' => 'boolean',
        'Enable_Notice_Accept' => 'boolean',
        'Enable_Notice_Arrived' => 'boolean',
        'Enable_Notice_InSession' => 'boolean',
        'Enable_Notice_Complete' => 'boolean',
        'Enable_Notice_Delete' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }
}
