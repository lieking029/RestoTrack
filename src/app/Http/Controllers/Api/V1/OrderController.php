<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menu_items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $order = $request->user()->orders()->create([
                'status' => 'PENDING',
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }

            return response()->json($order->load('items'), 201);
        });
    }

    public function complete(Order $order)
    {
        $this->authorize('complete', $order);

        $order->update([
            'status' => OrderStatus::COMPLETED,
        ]);

        return response()->json($order);
    }

    public function cancel(Order $order, InventoryService $inventoryService)
    {
        $this->authorize('cancel', $order);

        DB::transaction(function () use ($order, $inventoryService) {

            if ($order->status === OrderStatus::CONFIRMED) {
                $inventoryService->restoreForCancelledOrder($order, auth()->id());
            }

            $order->update([
                'status' => OrderStatus::CANCELLED,
            ]);
        });

        return response()->json($order->fresh(['items']));
    }
}
