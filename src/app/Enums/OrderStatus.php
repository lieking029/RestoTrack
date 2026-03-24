<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PENDING()
 * @method static static INPREPARATION()
 * @method static static READY()
 * @method static static SERVED()
 * @method static static COMPLETED()
 * @method static static CANCELLED()
 */
final class OrderStatus extends Enum
{
    const PENDING = 0;
    const INPREPARATION = 1;
    const READY = 2;
    const SERVED = 3;
    const COMPLETED = 4;
    const CANCELLED = 5;
}
