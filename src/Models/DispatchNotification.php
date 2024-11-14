<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchNotification extends Model
{
    // Table associated with the model
    public $incrementing = true;

    // Primary key for the table
    public $timestamps = false;

    // Disable auto-incrementing of the primary key (if necessary)
    protected $table = 'Dispatch_Notification'; // or false, if your ID is not auto-incremented

    // Disable timestamps if your table does not have 'created_at' and 'updated_at' columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
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
}
