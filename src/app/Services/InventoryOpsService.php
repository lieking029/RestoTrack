<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryOpsService
{
    public function receive(InventoryItem $item, int $qty, ?string $userId, ?string $note = null): void
    {
        DB::transaction(function () use ($item, $qty, $userId, $note) {
            $locked = InventoryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();

            $locked->increment('stock_quantity', $qty);

            // Sync Product model stock and status
            $locked->product->updateStock($qty, true);

            InventoryMovement::create([
                'inventory_item_id' => $locked->id,
                'order_id' => null,
                'performed_by' => $userId,
                'type' => 'CREDIT',
                'reason' => 'RECEIVING',
                'quantity' => $qty,
                'note' => $note,
            ]);
        });
    }

    public function waste(InventoryItem $item, int $qty, ?string $userId, ?string $note = null): void
    {
        DB::transaction(function () use ($item, $qty, $userId, $note) {
            $locked = InventoryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();

            if ($locked->stock_quantity < $qty) {
                abort(422, 'Waste quantity exceeds current stock.');
            }

            $locked->decrement('stock_quantity', $qty);

            // Sync Product model stock and status
            $locked->product->updateStock($qty);

            InventoryMovement::create([
                'inventory_item_id' => $locked->id,
                'order_id' => null,
                'performed_by' => $userId,
                'type' => 'DEBIT',
                'reason' => 'WASTE',
                'quantity' => $qty,
                'note' => $note,
            ]);
        });
    }

    public function adjustTo(InventoryItem $item, int $newQty, ?string $userId, ?string $note = null): void
    {
        DB::transaction(function () use ($item, $newQty, $userId, $note) {
            $locked = InventoryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();

            $current = (int) $locked->stock_quantity;
            $diff = $newQty - $current;

            if ($diff === 0) {
                return;
            }

            $locked->update(['stock_quantity' => $newQty]);

            // Sync Product model stock and status
            if ($diff > 0) {
                $locked->product->updateStock(abs($diff), true);
            } else {
                $locked->product->updateStock(abs($diff));
            }

            InventoryMovement::create([
                'inventory_item_id' => $locked->id,
                'order_id' => null,
                'performed_by' => $userId,
                'type' => $diff > 0 ? 'CREDIT' : 'DEBIT',
                'reason' => 'ADJUSTMENT',
                'quantity' => abs($diff),
                'note' => $note,
            ]);
        });
    }
}
