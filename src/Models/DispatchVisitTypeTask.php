<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchVisitTypeTask extends Model
{
    // Table associated with the model
    public $timestamps = false;

    // Primary key for the table
    protected $table = 'Dispatch_Visit_Type_Task';

    // Disable timestamps since the table does not have created_at and updated_at columns
    protected $primaryKey = 'Visit_Type_Task_GUID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'Company_Dept_ID',
        'Visit_Type_ID',
        'Visit_Type_Task_Priority',
        'Task_Description',
        'Visit_Type_Task_GUID',
        'Visit_Type_Task_Enabled',
    ];

    // Define the relationship with the DispatchVisitType model (assuming one-to-many relationship)
    public function visitType()
    {
        return $this->belongsTo(DispatchVisitType::class, 'Visit_Type_ID', 'ID');
    }
}
