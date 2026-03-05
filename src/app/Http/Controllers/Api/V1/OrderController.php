<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function my(Request $request)
    {
        return response()->json(
            $request->user()->orders()
                ->with(['items'])
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $menuIds = collect($data['items'])->pluck('menu_id');
            $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

            $subtotal = 0;

            $order = $request->user()->orders()->create([
                'status' => OrderStatus::PENDING,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                $itemTotal = $menu->price * $item['quantity'];
                $subtotal += $itemTotal;

                $order->items()->create([
                    'menu_id' => $item['menu_id'],
                    'name' => $menu->name,
                    'unit_price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                ]);
            }

            $tax = round($subtotal * 0.12, 2);
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);

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

            if ($order->status->is(OrderStatus::CONFIRMED)) {
                $inventoryService->restoreForCancelledOrder($order, auth()->id());
            }

            $order->update([
                'status' => OrderStatus::CANCELLED,
            ]);
        });

        return response()->json($order->fresh(['items']));
    }
}
