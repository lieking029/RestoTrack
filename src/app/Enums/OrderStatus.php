<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PENDING()
 * @method static static CONFIRMED()
 * @method static static INPREPARATION()
 * @method static static READY()
 * @method static static COMPLETED()
 * @method static static CANCELLED()
 */
final class OrderStatus extends Enum
{
    const PENDING = 0;
    const CONFIRMED = 1;
    const INPREPARATION = 2;
    const READY = 3;
    const COMPLETED = 4;
    const CANCELLED = 5;
}
