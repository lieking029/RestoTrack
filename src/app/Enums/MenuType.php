<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Appetizer()
 * @method static static MainCourse()
 * @method static static Dessert()
 * @method static static Beverage()
 * @method static static Snack()
 */
final class MenuType extends Enum
{
    const Appetizer = 0;
    const MainCourse = 1;
    const Dessert = 2;
    const Beverage = 3;
    const Snack = 4;

    /**
     * Get the description for each category.
     *
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        return match($value) {
            self::Appetizer => 'Appetizer',
            self::MainCourse => 'Main Course',
            self::Dessert => 'Dessert',
            self::Beverage => 'Beverage',
            self::Snack => 'Snack',
            default => parent::getDescription($value),
        };
    }

    /**
     * Get all categories for select dropdown.
     *
     * @return array
     */
    public static function toSelectArray(): array
    {
        return [
            self::Appetizer => self::getDescription(self::Appetizer),
            self::MainCourse => self::getDescription(self::MainCourse),
            self::Dessert => self::getDescription(self::Dessert),
            self::Beverage => self::getDescription(self::Beverage),
            self::Snack => self::getDescription(self::Snack),
        ];
    }

    /**
     * Get icon for category.
     *
     * @param int $value
     * @return string
     */
    public static function getIcon(int $value): string
    {
        return match($value) {
            self::Appetizer => 'fas fa-salad',
            self::MainCourse => 'fas fa-utensils',
            self::Dessert => 'fas fa-ice-cream',
            self::Beverage => 'fas fa-coffee',
            self::Snack => 'fas fa-cookie-bite',
            default => 'fas fa-utensils',
        };
    }

    /**
     * Get badge class for category.
     *
     * @param int $value
     * @return string
     */
    public static function getBadgeClass(int $value): string
    {
        return match($value) {
            self::Appetizer => 'info',
            self::MainCourse => 'primary',
            self::Dessert => 'warning',
            self::Beverage => 'success',
            self::Snack => 'secondary',
            default => 'secondary',
        };
    }
}