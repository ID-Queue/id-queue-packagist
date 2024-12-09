<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class AdminServiceSetting extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    // Specify the table associated with the model

    protected $table = 'Admin_Service_Settings';

    // If you want to allow mass assignment for these fields

    // If you have timestamps, enable this property
    protected $fillable = [
        'Enable_Pre_Schedual',
        'Enable_Life_Threat',
        'Enable_FN_LN',
        'Enable_Second_Person_ID',
        'Enable_Gender_Pref',
        'App_Location_Show',
        'App_Location_Detail_Show',
        'App_Zone_Show',
        'App_Building_Show',
        'Enable_Video_Conf',
        'Enable_Staff_Wrn',
        'Enable_Notice_Submit',
        'Enable_Notice_Accept',
        'Enable_Notice_Arrived',
        'Enable_Notice_InSession',
        'Enable_Notice_Complete',
        'Enable_Notice_Delete',
        'Enable_Single_Req',
        'Single_Req_By',
        'Enable_Auto_Logout',
        'Auto_Logout_Time',
        'Pre_Schedual_Metric',
        'Pre_Schedual_Num',
        'Enable_Comp_Sign',
        'Enable_Req_If_Staff_Online',
        'Msg_If_Staff_Off_Line',
        'Enable_Ext_Queue',
        'Enable_Dispatch',
        'Presched_Time_Interval',
        'Default_Time_Zone',
    ]; // Set to true if your table has 'created_at' and 'updated_at' columns

    // Specify the casts for certain fields
    protected $casts = [
        'Enable_Pre_Schedual' => 'boolean',
        'Enable_Life_Threat' => 'boolean',
        'Enable_FN_LN' => 'boolean',
        'Enable_Second_Person_ID' => 'boolean',
        'Enable_Gender_Pref' => 'boolean',
        'App_Location_Show' => 'boolean',
        'App_Location_Detail_Show' => 'boolean',
        'App_Zone_Show' => 'boolean',
        'App_Building_Show' => 'boolean',
        'Enable_Video_Conf' => 'boolean',
        'Enable_Staff_Wrn' => 'boolean',
        'Enable_Notice_Submit' => 'boolean',
        'Enable_Notice_Accept' => 'boolean',
        'Enable_Notice_Arrived' => 'boolean',
        'Enable_Notice_InSession' => 'boolean',
        'Enable_Notice_Complete' => 'boolean',
        'Enable_Notice_Delete' => 'boolean',
        'Enable_Single_Req' => 'boolean',
        'Enable_Auto_Logout' => 'boolean',
        'Pre_Schedual_Metric' => 'string',
        'Pre_Schedual_Num' => 'integer',
        'Enable_Comp_Sign' => 'boolean',
        'Enable_Req_If_Staff_Online' => 'boolean',
        'Enable_Ext_Queue' => 'boolean',
        'Enable_Dispatch' => 'boolean',
        'Presched_Time_Interval' => 'string',
        'Default_Time_Zone' => 'string',
    ];

    // Method to get settings with aliases
    public function getSettingsWithAliases(): array
    {
        return [
            'schedVal' => $this->Enable_Pre_Schedual,
            'lifeThrVal' => $this->Enable_Life_Threat,
            'FnLnVal' => $this->Enable_FN_LN,
            'secPerVal' => $this->Enable_Second_Person_ID,
            'gendVal' => $this->Enable_Gender_Pref,
            'appLocShow' => $this->App_Location_Show,
            'appLocDetailShow' => $this->App_Location_Detail_Show,
            'appZoneShow' => $this->App_Zone_Show,
            'appBuildHide' => $this->App_Building_Show,
            'vidVal' => $this->Enable_Video_Conf,
            'alrtVal' => $this->Enable_Staff_Wrn,
            'alrtValReqSub' => $this->Enable_Notice_Submit,
            'alrtValReqAcc' => $this->Enable_Notice_Accept,
            'alrtValReqArv' => $this->Enable_Notice_Arrived,
            'alrtValReqSes' => $this->Enable_Notice_InSession,
            'alrtValReqCmp' => $this->Enable_Notice_Complete,
            'alrtValReqDlt' => $this->Enable_Notice_Delete,
            'snglReqVal' => $this->Enable_Single_Req,
            'snglReqByVal' => $this->Single_Req_By,
            'appAutoOut' => $this->Enable_Auto_Logout,
            'appAutoOutTime' => $this->Auto_Logout_Time,
            'preSchedMet' => $this->Pre_Schedual_Metric,
            'preSchedNum' => $this->Pre_Schedual_Num,
            'signVal' => $this->Enable_Comp_Sign,
            'reqOnlineVal' => $this->Enable_Req_If_Staff_Online,
            'reqOnlineMsg' => $this->Msg_If_Staff_Off_Line,
            'ExtQueVal' => $this->Enable_Ext_Queue,
            'enableDispatch' => $this->Enable_Dispatch,
            'timezone' => $this->Default_Time_Zone,
        ];
    }

    public static function getSettingFor($settingName)
    {
        // Get the first record and return the value of the specified setting
        $setting = self::get($settingName)->first();
        if (isset($setting->{$settingName})) {
            return $setting->{$settingName};
        }

        return false;
    }
}
