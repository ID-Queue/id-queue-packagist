<?php

namespace IdQueue\IdQueuePackagist\Utils;

use IdQueue\IdQueuePackagist\Utils\Traits\{
    AccessLogging,
    AdminUtility,
    BuildingUtility,
    DatabaseUtility,
    JwtUtility,
    NotificationUtility,
    PasswordValidation,
    RequestUtility,
    ServiceHourUtility,
    ServiceUtility,
    StaffUtility,
    UserInfo
};

class Helper
{
    use AccessLogging,
        AdminUtility,
        BuildingUtility,
        DatabaseUtility,
        JwtUtility,
        NotificationUtility,
        PasswordValidation,
        RequestUtility,
        ServiceHourUtility,
        ServiceUtility,
        StaffUtility,
        UserInfo;
}
