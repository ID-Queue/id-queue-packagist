<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class DispatchWorkFlowBoardOption extends Model
{
    use CompanyDbConnection;

    // Table associated with the model
    public $incrementing = false;

    // Primary key for the table

    // The type of the primary key field (UUID)
    protected $table = 'Dispatch_WorkFlow_Board_Options'; // since GUID is not auto-incrementing

    // Disable timestamps since the table does not have created_at and updated_at columns
    protected $primaryKey = 'GUID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'GUID',
        'Company_Dept_ID',
        'ID_Building',
        'Show_Pat_Ident',
        'Show_Sec_Pat_Ident_Char',
        'Show_Staff',
        'Show_Sec_Name_Char',
        'Show_Req_Time',
        'Show_Service',
        'Show_Location',
        'Show_Zone',
        'Show_Building',
        'Show_Visit_Type',
        'Show_Approved',
        'Show_Arrived',
        'Show_Session',
        'Show_Paused',
        'Show_Req_By',
        'Show_Gender_Pref',
        'Show_Pre_Sched',
        'Show_Wait_Time',
        'Show_Logo',
        'Show_Starting_Notes',
        'Show_Status_Icon',
        'Display_Width',
        'Display_Height',
        'Display_Max_Limit',
        'Refresh_Rate',
        'Font_Size',
    ];
}
