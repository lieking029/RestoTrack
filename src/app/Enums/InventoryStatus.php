<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OnStock()
 * @method static static LowOnStock()
 * @method static static NoStock()
 */
final class InventoryStatus extends Enum
{
    const OnStock = 0;
    const LowOnStock = 1;
    const NoStock = 2;
}
