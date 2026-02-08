<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        return OrderResource::collection(
            Order::whereIn('status', ['CONFIRMED', 'IN_PREPARATION'])
                ->with(['items', 'creator.roles', 'processor.roles'])
                ->latest()
                ->get()
        );
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:IN_PREPARATION,READY'],
        ]);

        if (!in_array($order->status, ['CONFIRMED', 'IN_PREPARATION'])) {
            abort(422, 'Invalid order state.');
        }

        $order->update(['status' => $data['status']]);

        return new OrderResource($order->load(['items', 'creator.roles']));
    }
}
