<?php

namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Low()
 * @method static static Medium()
 * @method static static High()
 * @method static static Critical()
 */
final class RequestPriority extends Enum
{
    const Low = 3;

    const Medium = 2;

    const High = 1;

    const Critical = 4;


    /**
     * Get the color code associated with the priority.
     */
    public static function color(mixed $value): string
    {
        return match ($value) {
            self::Low => '#00CC33', // Green
            self::Medium => '#FF9933', // Orange
            self::High => '#FF0000', // Red
            self::Critical => '#405260', // Dark Gray
            default => '#000000', // Default to black for unknown values
        };
    }

    /**
     * Get the label associated with the priority.
     */
    public static function label(mixed $value): string
    {
        return match ($value) {
            self::Low => 'Low Priority',
            self::Medium => 'Medium Priority',
            self::High => 'High Priority',
            self::Critical => 'Critical Priority',
            default => 'Unknown Priority',
        };
    }
}
