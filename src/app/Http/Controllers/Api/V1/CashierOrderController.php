<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class CashierOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'creator.roles'])->latest();

        if ($request->has('status')) {
            $query->where('status', $request->integer('status'));
        }

        return OrderResource::collection($query->get());
    }
}
