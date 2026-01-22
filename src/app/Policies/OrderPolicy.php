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
            && $order->status === OrderStatus::PENDING;
    }

    public function cancel(User $user, Order $order): bool
    {
        return in_array($user->getRoleNames()->first(), ['cashier', 'manager'], true)
            && in_array($order->status, [
                OrderStatus::PENDING,
                OrderStatus::CONFIRMED,
            ], true);
    }

    public function startPreparation(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['cook', 'chef'])
            && $order->status === OrderStatus::CONFIRMED;
    }

    public function markReady(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['cook', 'chef'])
            && $order->status === OrderStatus::INPREPARATION;
    }

    public function complete(User $user, Order $order): bool
    {
        return $user->hasRole('cashier')
            && $order->status === OrderStatus::READY;
    }
}