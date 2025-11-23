<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static Manager()
 * @method static static Employee()
 */
final class UserType extends Enum
{
    const Admin = 0;
    const Manager = 1;
    const Employee = 2;

    /**
     * Get the description for each user type.
     *
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        return match($value) {
            self::Admin => 'Administrator',
            self::Manager => 'Manager',
            self::Employee => 'Employee',
            default => parent::getDescription($value),
        };
    }

    /**
     * Get all user types for select dropdown.
     *
     * @return array
     */
    public static function toSelectArray(): array
    {
        return [
            self::Admin => self::getDescription(self::Admin),
            self::Manager => self::getDescription(self::Manager),
            self::Employee => self::getDescription(self::Employee),
        ];
    }

    /**
     * Get user types excluding admin.
     *
     * @return array
     */
    public static function nonAdminTypes(): array
    {
        return [
            self::Manager => self::getDescription(self::Manager),
            self::Employee => self::getDescription(self::Employee),
        ];
    }

    /**
     * Get badge class for UI display.
     *
     * @param int $value
     * @return string
     */
    public static function getBadgeClass(int $value): string
    {
        return match($value) {
            self::Admin => 'danger',
            self::Manager => 'primary',
            self::Employee => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get icon for UI display.
     *
     * @param int $value
     * @return string
     */
    public static function getIcon(int $value): string
    {
        return match($value) {
            self::Admin => 'fas fa-crown',
            self::Manager => 'fas fa-user-tie',
            self::Employee => 'fas fa-user',
            default => 'fas fa-user',
        };
    }
}