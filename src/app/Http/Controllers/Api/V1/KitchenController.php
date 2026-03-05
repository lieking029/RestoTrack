<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        return OrderResource::collection(
            Order::whereIn('status', [OrderStatus::CONFIRMED, OrderStatus::INPREPARATION, OrderStatus::READY])
                ->with(['items', 'creator.roles', 'processor.roles'])
                ->latest()
                ->get()
        );
    }

    public function updateStatus(Request $request, Order $order)
    {
        $statusMap = [
            'inPreparation' => OrderStatus::INPREPARATION,
            'ready' => OrderStatus::READY,
        ];

        $policyMap = [
            'inPreparation' => 'startPreparation',
            'ready' => 'markReady',
        ];

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys($statusMap))],
        ]);

        $this->authorize($policyMap[$data['status']], $order);

        $order->update(['status' => $statusMap[$data['status']]]);

        return new OrderResource($order->load(['items', 'creator.roles']));
    }
}
