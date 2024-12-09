<?php

namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum representing various application settings.
 * These constants are used for managing various configurations
 * related to scheduling, user preferences, notifications, and more.
 *
 * @method static static Enable_Pre_Schedual()
 * @method static static Pre_Schedual_Metric()
 * @method static static Pre_Schedual_Num()
 * @method static static Presched_Time_Interval()
 * @method static static App_Location_Show()
 * @method static static App_Location_Detail_Show()
 * @method static static App_Zone_Show()
 * @method static static App_Building_Show()
 * @method static static Enable_FN_LN()
 * @method static static Enable_Second_Person_ID()
 * @method static static Enable_Gender_Pref()
 * @method static static Enable_Video_Conf()
 * @method static static Enable_Notice_Submit()
 * @method static static Enable_Notice_Accept()
 * @method static static Enable_Notice_Arrived()
 * @method static static Enable_Notice_InSession()
 * @method static static Enable_Notice_Complete()
 * @method static static Enable_Notice_Delete()
 * @method static static Enable_Staff_Wrn()
 * @method static static Enable_Req_If_Staff_Online()
 * @method static static Msg_If_Staff_Off_Line()
 * @method static static Enable_Single_Req()
 * @method static static Single_Req_By()
 * @method static static Enable_Auto_Logout()
 * @method static static Auto_Logout_Time()
 * @method static static Enable_Ext_Queue()
 * @method static static Enable_Dispatch()
 * @method static static Enable_Comp_Sign()
 * @method static static Enable_Life_Threat()
 * @method static static Default_Time_Zone()
 */
class AppSettings extends Enum
{
    // Scheduling settings
    const Enable_Pre_Schedual = 'Enable_Pre_Schedual'; // Enables pre-scheduling feature

    const Pre_Schedual_Metric = 'Pre_Schedual_Metric'; // Defines the metric for pre-scheduling

    const Pre_Schedual_Num = 'Pre_Schedual_Num'; // Defines the number of pre-schedules allowed

    const Presched_Time_Interval = 'Presched_Time_Interval'; // Defines the time interval for pre-scheduling

    // Application display settings
    const App_Location_Show = 'App_Location_Show'; // Controls whether location is shown in the app

    const App_Location_Detail_Show = 'App_Location_Detail_Show'; // Controls whether location details are shown

    const App_Zone_Show = 'App_Zone_Show'; // Controls whether zone information is displayed

    const App_Building_Show = 'App_Building_Show'; // Controls whether building information is displayed

    // User preferences
    const Enable_FN_LN = 'Enable_FN_LN'; // Enable first and last name entry for users

    const Enable_Second_Person_ID = 'Enable_Second_Person_ID'; // Enables second person identification for users

    const Enable_Gender_Pref = 'Enable_Gender_Pref'; // Enables gender preference option for users

    // Video conferencing
    const Enable_Video_Conf = 'Enable_Video_Conf'; // Enables video conferencing feature for the app

    // Notification settings
    const Enable_Notice_Submit = 'Enable_Notice_Submit'; // Enables submission of notices

    const Enable_Notice_Accept = 'Enable_Notice_Accept'; // Enables acceptance of notices

    const Enable_Notice_Arrived = 'Enable_Notice_Arrived'; // Enables notification when something arrives

    const Enable_Notice_InSession = 'Enable_Notice_InSession'; // Enables notification for in-session updates

    const Enable_Notice_Complete = 'Enable_Notice_Complete'; // Enables notification when a task is complete

    const Enable_Notice_Delete = 'Enable_Notice_Delete'; // Enables deletion of notices

    // Staff settings
    const Enable_Staff_Wrn = 'Enable_Staff_Wrn'; // Enables staff warning feature

    const Enable_Req_If_Staff_Online = 'Enable_Req_If_Staff_Online'; // Allows request if staff is online

    const Msg_If_Staff_Off_Line = 'Msg_If_Staff_Off_Line'; // Sends message when staff is offline

    // Single request settings
    const Enable_Single_Req = 'Enable_Single_Req'; // Enables single request mode

    const Single_Req_By = 'Single_Req_By'; // Defines who can initiate a single request

    // Auto logout settings
    const Enable_Auto_Logout = 'Enable_Auto_Logout'; // Enables auto logout feature

    const Auto_Logout_Time = 'Auto_Logout_Time'; // Defines the time before auto logout occurs

    // Queue and dispatch settings
    const Enable_Ext_Queue = 'Enable_Ext_Queue'; // Enables external queue management

    const Enable_Dispatch = 'Enable_Dispatch'; // Enables dispatch feature for requests

    // Compliance settings
    const Enable_Comp_Sign = 'Enable_Comp_Sign'; // Enables compliance signature feature

    // Other settings
    const Enable_Life_Threat = 'Enable_Life_Threat'; // Enables life-threatening situation flag

    const Default_Time_Zone = 'Default_Time_Zone'; // Sets the default time zone for the application
}
