<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DispatchDepartment extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = false;

    // Primary key for the table
    public $timestamps = false;

    // Disable auto-incrementing of the primary key (since the ID is not auto-incremented in your table)
    protected $table = 'Dispatch_Departments';

    // Disable timestamps if your table does not have 'created_at' and 'updated_at' columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'ID',
        'Company_Code',
        'Company_Dept',
    ];

    /**
     * Define the relationship to DispatchChart.
     */
    public function dispatchCharts(): HasMany
    {
        return $this->hasMany(DispatchChart::class, 'Company_Dept_ID', 'ID');
    }

    /**
     * Define the relationship to DispatchChartDetails.
     */
    public function dispatchChartDetails(): HasMany
    {
        return $this->hasMany(DispatchChartDetails::class, 'Company_Dept_ID', 'ID');
    }

    /**
     * Define the relationship to AdminLog.
     */
    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'Company_Dept_ID', 'ID');
    }

    /**
     * Define the relationship to AdminStaff.
     */
    public function adminStaff(): HasMany
    {
        return $this->hasMany(AdminStaff::class, 'Company_Dept_ID', 'ID');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(DeptPreSetting::class, 'Company_Dept_ID', 'ID');
    }
}
