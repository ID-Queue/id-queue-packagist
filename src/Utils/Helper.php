<?php

namespace IdQueue\IdQueuePackagist\Utils;

use IdQueue\IdQueuePackagist\Utils\Traits\AccessLogging;
use IdQueue\IdQueuePackagist\Utils\Traits\AdminUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\BuildingUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\DatabaseUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\JwtUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\NotificationUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\PasswordValidation;
use IdQueue\IdQueuePackagist\Utils\Traits\RequestUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\ServiceHourUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\ServiceUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\StaffUtility;
use IdQueue\IdQueuePackagist\Utils\Traits\UserInfo;

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
