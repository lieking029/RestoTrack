<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;

class CashierOrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(
            Order::where('status', OrderStatus::PENDING)
                ->with(['items', 'creator.roles'])
                ->latest()
                ->get()
        );
    }
}
