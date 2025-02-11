<?php

namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;

final class ServiceCustomizationEnum extends Enum
{
    const SERVICE_CUSTOMIZATION = 'Service Customization';

    const SERVICE_REQUEST_FEATURES = 'Service Request Features';

    const LOCATIONS = 'Locations';

    const SERVICE_REQUEST_RESTRICTION = 'Service Request Restriction';

    const AUTO_LOGOUT_AND_AFTER_HOURS = 'Auto Logout & After Hours';

    const EXTERNAL_WORKFLOW_BOARD = 'External Workflow Board';

    const SCHEDULING = 'Scheduling';

    const EMAIL_ALERTS = 'Email Alerts';

    const DISPATCH = 'Dispatch';

    /**
     * Get the name associated with the enum value.
     *
     * @param  string  $value
     */
    public static function getName($value): ?string
    {
        return self::getValue($value);
    }

    /**
     * Get the image URL associated with the enum value.
     *
     * @param  string  $value
     */
    public static function getImage($value): ?string
    {
        // Map enum values to image URLs
        $images = [
            self::SERVICE_CUSTOMIZATION => 'building.png',
            self::SERVICE_REQUEST_FEATURES => 'select.png',
            self::LOCATIONS => 'settings.png',
            self::SERVICE_REQUEST_RESTRICTION => 'earth-stop.png',
            self::AUTO_LOGOUT_AND_AFTER_HOURS => 'out.png',
            self::EXTERNAL_WORKFLOW_BOARD => 'tv.png',
            self::SCHEDULING => 'datetime_17.png',
            self::EMAIL_ALERTS => 'email.png',
            self::DISPATCH => 'dispatcher_25.png',
        ];

        return $images[$value] ?? null;
    }
}
