<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function pay(User $user, Order $order): bool
    {
        return $user->hasRole('cashier')
            && $order->status->value === OrderStatus::SERVED;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['cashier', 'manager', 'server', 'barista'])
            && in_array($order->status->value, [
                OrderStatus::PENDING,
                OrderStatus::INPREPARATION,
            ], true);
    }

    public function startPreparation(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['cook', 'chef'])
            && $order->status->value === OrderStatus::PENDING;
    }

    public function markReady(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['cook', 'chef'])
            && $order->status->value === OrderStatus::INPREPARATION;
    }

    public function serve(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['server', 'barista'])
            && $order->status->value === OrderStatus::READY;
    }

    public function complete(User $user, Order $order): bool
    {
        return $user->hasRole('cashier')
            && $order->status->value === OrderStatus::SERVED;
    }
}
