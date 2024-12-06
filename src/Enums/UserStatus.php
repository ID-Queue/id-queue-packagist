<?php

namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Available()
 * @method static static Dispatched()
 * @method static static Lunch()
 * @method static static NotAvailable()
 * @method static static Paused()
 * @method static static InProgress()
 * @method static static Arrived()
 * @method static static Accepted()
 * @method static static CheckOut()
 */
final class UserStatus extends Enum
{
    const Available = 0;

    const Dispatched = 1;

    const Lunch = 2;

    const NotAvailable = 3;

    const Paused = 4;

    const InProgress = 5;

    const Arrived = 6;

    const Accepted = 7;

    const CheckOut = 8;

    /**
     * Get the image name associated with the status.
     */
    public static function image(mixed $value): string
    {
        return match ($value) {
            self::Available => 'in.png',
            self::Dispatched => 'dispatcher_25.png',
            self::Lunch => 'lunch.png',
            self::NotAvailable => 'out-na.png',
            self::Paused => 'pause.png',
            self::InProgress => 'inSession.png',
            self::Arrived => 'sw.png',
            self::Accepted => 'thumbs.png',
            self::CheckOut => 'out.png',
        };
    }

    /**
     * Get the note associated with the status.
     */
    public static function note(mixed $value): string
    {
        return match ($value) {
            self::Available => 'Available',
            self::Dispatched => 'Dispatched',
            self::Lunch => 'Lunch',
            self::NotAvailable => 'Not Available',
            self::Paused => 'Paused',
            self::InProgress => 'InProgress',
            self::Arrived => 'Arrived',
            self::Accepted => 'Accepted',
            self::CheckOut => 'Check out',
        };
    }
}
