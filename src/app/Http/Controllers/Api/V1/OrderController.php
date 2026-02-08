<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Menu;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function my(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items', 'creator.roles', 'processor.roles'])
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }
    /**
     * Handle the incoming request.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $order = $request->user()->orders()->create([
                'status' => OrderStatus::PENDING(),
            ]);

            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $lineTotal = $menu->price * $item['quantity'];
                $subtotal += $lineTotal;

                $order->items()->create([
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'unit_price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'total' => $lineTotal,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            return new OrderResource($order->load(['items']));
        });
    }

    public function complete(Order $order)
    {
        $this->authorize('complete', $order);

        $order->update([
            'status' => OrderStatus::COMPLETED,
        ]);

        return new OrderResource($order);
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

        return new OrderResource($order);
    }
}
