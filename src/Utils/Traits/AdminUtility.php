<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use DB;
use IdQueue\IdQueuePackagist\Models\Company\AdminServiceSetting;
use InvalidArgumentException;

trait AdminUtility
{
    public static function getAdditionalAdminValue(int $dept_ID): array
    {
        $data = AdminServiceSetting::where('Company_Dept_ID', $dept_ID)
            ->pluck(
                'Enable_Pre_Schedual', 'Enable_Life_Threat', 'Enable_FN_LN',
                'Enable_Second_Person_ID', 'Enable_Gender_Pref', 'App_Location_Show',
                'App_Location_Detail_Show', 'App_Zone_Show', 'App_Building_Show',
                'Enable_Video_Conf', 'Enable_Staff_Wrn', 'Enable_Notice_Submit',
                'Enable_Notice_Accept', 'Enable_Notice_Arrived', 'Enable_Notice_InSession',
                'Enable_Notice_Complete', 'Enable_Notice_Delete', 'Enable_Single_Req',
                'Single_Req_By', 'Enable_Auto_Logout', 'Auto_Logout_Time',
                'Pre_Schedual_Metric', 'Pre_Schedual_Num', 'Enable_Comp_Sign',
                'Enable_Req_If_Staff_Online', 'Msg_If_Staff_Off_Line',
                'Enable_Ext_Queue', 'Enable_Dispatch', 'Presched_Time_Interval',
                'Default_Time_Zone'
            )
            ->first();

        return $data ? $data->toArray() : [];
    }

    public static function getSingleAdminSetting($dept_ID, $settingName)
    {
        // Define the allowed columns to ensure the requested setting is valid
        $columns = [
            'Enable_Pre_Schedual', 'Enable_Life_Threat', 'Enable_FN_LN', 'Enable_Second_Person_ID', 'Enable_Gender_Pref',
            'App_Location_Show', 'App_Location_Detail_Show', 'App_Zone_Show', 'App_Building_Show', 'Enable_Video_Conf',
            'Enable_Staff_Wrn', 'Enable_Notice_Submit', 'Enable_Notice_Accept', 'Enable_Notice_Arrived', 'Enable_Notice_InSession',
            'Enable_Notice_Complete', 'Enable_Notice_Delete', 'Enable_Single_Req', 'Single_Req_By', 'Enable_Auto_Logout',
            'Auto_Logout_Time', 'Pre_Schedual_Metric', 'Pre_Schedual_Num', 'Enable_Comp_Sign', 'Enable_Req_If_Staff_Online',
            'Msg_If_Staff_Off_Line', 'Enable_Ext_Queue', 'Enable_Dispatch', 'Presched_Time_Interval', 'Default_Time_Zone',
        ];

        // Check if the requested setting is valid
        if (! in_array($settingName, $columns)) {
            throw new InvalidArgumentException("Invalid setting name: $settingName");
        }

        // Fetch the specific setting for the given department ID
        return DB::table('Admin_Service_Settings')
            ->where('Company_Dept_ID', $dept_ID)
            ->value($settingName); // Efficiently retrieves the single column value
    }
}
