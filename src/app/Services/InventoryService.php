<?php

namespace App\Services;

use App\Models\Order;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;

class InventoryService
{
    public function deductForPaidOrder(Order $order, string $userId): void
    {
        $order->loadMissing(['items.menu.products']);

        foreach ($order->items as $orderItem) {
            $menu = $orderItem->menu;

            foreach ($menu->products as $product) {
                $requiredQty = (int) ceil($product->pivot->quantity_needed * $orderItem->quantity);

                $inventory = InventoryItem::where('product_id', $product->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($inventory->stock_quantity < $requiredQty) {
                    abort(422, "Insufficient stock for ingredient: {$product->name}");
                }

                $inventory->decrement('stock_quantity', $requiredQty);

                InventoryMovement::create([
                    'inventory_item_id' => $inventory->id,
                    'order_id' => $order->id,
                    'performed_by' => $userId,
                    'type' => 'DEBIT',
                    'reason' => 'SALE',
                    'quantity' => $requiredQty,
                    'note' => "Deducted for paid order",
                ]);
            }
        }
    }

    public function restoreForCancelledOrder(Order $order, string $userId): void
    {
        $order->loadMissing(['items.menu.products']);

        foreach ($order->items as $orderItem) {
            $menu = $orderItem->menu;

            foreach ($menu->products as $product) {
                $restoreQty = (int) ceil($product->pivot->quantity_needed * $orderItem->quantity);

                $inventory = InventoryItem::where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    continue;
                }

                $inventory->increment('stock_quantity', $restoreQty);

                InventoryMovement::create([
                    'inventory_item_id' => $inventory->id,
                    'order_id' => $order->id,
                    'performed_by' => $userId,
                    'type' => 'CREDIT',
                    'reason' => 'CANCEL',
                    'quantity' => $restoreQty,
                    'note' => "Restored due to cancellation",
                ]);
            }
        }
    }
}
