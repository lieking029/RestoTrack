<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Available()
 * @method static static Unavailable()
 */
final class MenuStatus extends Enum
{
    const Available = 0;
    const Unavailable = 1;

    /**
     * Get the description for each status.
     *
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        return match($value) {
            self::Available => 'Available',
            self::Unavailable => 'Unavailable',
            default => parent::getDescription($value),
        };
    }

    /**
     * Get badge class for status.
     *
     * @param int $value
     * @return string
     */
    public static function getBadgeClass(int $value): string
    {
        return match($value) {
            self::Available => 'success',
            self::Unavailable => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get icon for status.
     *
     * @param int $value
     * @return string
     */
    public static function getIcon(int $value): string
    {
        return match($value) {
            self::Available => 'fas fa-check-circle',
            self::Unavailable => 'fas fa-times-circle',
            default => 'fas fa-question-circle',
        };
    }
}