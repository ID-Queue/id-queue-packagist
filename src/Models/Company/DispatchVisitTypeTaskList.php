<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class DispatchVisitTypeTaskList extends Model
{
    use CompanyDbConnection;

    // Table associated with the model

    // Primary key for the table
    protected $table = 'Dispatch_Visit_Type_Task_List';

    // Disable timestamps since the table does not have created_at and updated_at columns
    protected $primaryKey = 'GUID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'GUID',
        'Company_Dept_ID',
        'Visit_Type_ID',
        'Visit_Type_Task_Priority',
        'Task_Description',
        'Task_Complete',
        'Dispatch_Chart',
        'Visit_Type_Task_GUID',
    ];

    // Define the relationship with the DispatchVisitTypeTask model (assuming a one-to-many relationship)
    public function visitTypeTask()
    {
        return $this->belongsTo(DispatchVisitTypeTask::class, 'Visit_Type_Task_GUID', 'Visit_Type_Task_GUID');
    }
}
