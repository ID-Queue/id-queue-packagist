<?php

declare(strict_types=1);

namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;

final class ServiceKeyEnum extends Enum
{
    const SERVICE_CUSTOMIZATION = 'SERVICE_CUSTOMIZATION';

    const SERVICE_REQUEST_FEATURES = 'SERVICE_REQUEST_FEATURES';

    const LOCATIONS = 'LOCATIONS';

    const SERVICE_REQUEST_RESTRICTION = 'LOCATIONS';

    const AUTO_LOGOUT_AND_AFTER_HOURS = 'AUTO_LOGOUT_AND_AFTER_HOURS';

    const EXTERNAL_WORKFLOW_BOARD = 'EXTERNAL_WORKFLOW_BOARD';

    const SCHEDULING = 'SCHEDULING';

    const EMAIL_ALERTS = 'EMAIL_ALERTS';

    const DISPATCH = 'DISPATCH';
}
