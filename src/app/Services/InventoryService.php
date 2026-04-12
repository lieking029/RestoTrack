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

        // Aggregate total required quantity per product across all order items
        $aggregated = [];
        foreach ($order->items as $orderItem) {
            foreach ($orderItem->menu->products as $product) {
                $requiredQty = $product->pivot->quantity_needed * $orderItem->quantity;
                $productId = $product->id;

                if (!isset($aggregated[$productId])) {
                    $aggregated[$productId] = [
                        'product' => $product,
                        'total_qty' => 0,
                    ];
                }

                $aggregated[$productId]['total_qty'] += $requiredQty;
            }
        }

        // Deduct once per product with the combined quantity
        foreach ($aggregated as $entry) {
            $product = $entry['product'];
            $requiredQty = $entry['total_qty'];

            $inventory = InventoryItem::where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                $inventory = InventoryItem::create([
                    'product_id' => $product->id,
                    'stock_quantity' => $product->remaining_stock ?? 0,
                    'reorder_level' => 0,
                ]);
            }

            if ($inventory->stock_quantity < $requiredQty) {
                abort(422, "Insufficient stock for ingredient: {$product->name}");
            }

            $inventory->decrement('stock_quantity', $requiredQty);

            // Sync Product model stock and status
            $product->refresh();
            $product->updateStock($requiredQty);

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

    public function restoreForCancelledOrder(Order $order, string $userId): void
    {
        $order->loadMissing(['items.menu.products']);

        // Aggregate total restore quantity per product across all order items
        $aggregated = [];
        foreach ($order->items as $orderItem) {
            foreach ($orderItem->menu->products as $product) {
                $restoreQty = $product->pivot->quantity_needed * $orderItem->quantity;
                $productId = $product->id;

                if (!isset($aggregated[$productId])) {
                    $aggregated[$productId] = [
                        'product' => $product,
                        'total_qty' => 0,
                    ];
                }

                $aggregated[$productId]['total_qty'] += $restoreQty;
            }
        }

        // Restore once per product with the combined quantity
        foreach ($aggregated as $entry) {
            $product = $entry['product'];
            $restoreQty = $entry['total_qty'];

            $inventory = InventoryItem::where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                continue;
            }

            $inventory->increment('stock_quantity', $restoreQty);

            // Sync Product model stock and status
            $product->refresh();
            $product->updateStock($restoreQty, true);

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
