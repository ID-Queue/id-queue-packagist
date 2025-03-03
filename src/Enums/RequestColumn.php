<?php

namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Collection;

/**
 * Enum representing various column identifiers used in requests.
 * Each constant corresponds to a specific column that appears in the request lifecycle,
 * such as Status, Date, Person ID, Service, etc. These constants help standardize column
 * identifiers across the application, ensuring consistency and reducing errors.
 *
 * @method static static STATUS()
 * @method static static DATE()
 * @method static static PERSON_ID()
 * @method static static SECOND_PERSON_ID()
 * @method static static BUILDING()
 * @method static static ZONE()
 * @method static static LOCATION()
 * @method static static LOCATION_DETAIL()
 * @method static static SERVICE()
 * @method static static VISIT_TYPE()
 * @method static static ASSIGN_STAFF()
 * @method static static REQUESTER_ID()
 * @method static static EXT()
 * @method static static REQUESTED_TIME()
 * @method static static APPOINTMENT_DATE()
 */
class RequestColumn extends Enum
{
    /**
     * Column ID for the 'Status' column.
     *
     * @var string
     */
    const STATUS = 'status';

    /**
     * Column ID for the 'Date' column.
     *
     * @var string
     */
    const DATE = 'date';

    /**
     * Column ID for the 'Person ID' column.
     *
     * @var string
     */
    const PERSON_ID = 'person_id';

    /**
     * Column ID for the 'Second Person ID' column.
     *
     * @var string
     */
    const SECOND_PERSON_ID = 'second_person_id';

    /**
     * Column ID for the 'Building' column.
     *
     * @var string
     */
    const BUILDING = 'building';

    /**
     * Column ID for the 'Zone' column.
     *
     * @var string
     */
    const ZONE = 'zone';

    /**
     * Column ID for the 'Location' column.
     *
     * @var string
     */
    const LOCATION = 'location';

    /**
     * Column ID for the 'Location Detail' column.
     *
     * @var string
     */
    const LOCATION_DETAIL = 'location_detail';

    /**
     * Column ID for the 'Service' column.
     *
     * @var string
     */
    const SERVICE = 'service';

    /**
     * Column ID for the 'Visit Type' column.
     *
     * @var string
     */
    const VISIT_TYPE = 'visit_type';

    /**
     * Column ID for the 'Assign Staff' column.
     *
     * @var string
     */
    const ASSIGN_STAFF = 'assign_staff';

    /**
     * Column ID for the 'Requester ID' column.
     *
     * @var string
     */
    const REQUESTER_ID = 'requester_id';

    /**
     * Column ID for the 'Ext' column.
     *
     * @var string
     */
    const EXT = 'ext';

    /**
     * Column ID for the 'Requested Time' column.
     *
     * @var string
     */
    const REQUESTED_TIME = 'requested_time';

    /**
     * Column ID for the 'Appointment Date' column.
     *
     * @var string
     */
    const APPOINTMENT_DATE = 'appointment_date';

    /**
     * Get a list of all column labels with their associated values.
     *
     * This function returns a collection of all the labels with their corresponding identifiers
     * from the enum. It can be used to dynamically retrieve column information.
     */
    public static function getColumnLabels(): Collection
    {
        return collect([
            self::STATUS => 'Status',
            self::DATE => 'Date',
            self::PERSON_ID => 'Person ID',
            self::SECOND_PERSON_ID => 'Second Person ID',
            self::BUILDING => 'Building',
            self::ZONE => 'Zone',
            self::LOCATION => 'Location',
            self::LOCATION_DETAIL => 'Location Detail',
            self::SERVICE => 'Service',
            self::VISIT_TYPE => 'Visit Type',
            self::ASSIGN_STAFF => 'Assign Staff',
            self::REQUESTER_ID => 'Requester ID',
            self::EXT => 'Ext',
            self::REQUESTED_TIME => 'Requested Time',
            self::APPOINTMENT_DATE => 'Appointment Date',
        ]);
    }

    /**
     * Get the label associated with a specific column ID.
     *
     * This function accepts a column ID and returns its corresponding label. It is useful for
     * retrieving the label of any column using its unique identifier.
     */
    public static function getLabelById(string $columnId): ?string
    {
        $labels = self::getColumnLabels();

        return $labels->get($columnId);
    }
}
