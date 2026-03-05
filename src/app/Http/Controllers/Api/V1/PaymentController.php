<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(public InventoryService $inventoryService)
    {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $order = Order::lockForUpdate()->findOrFail($data['order_id']);

            $this->authorize('pay', $order);

            if (!$order->status->is(OrderStatus::PENDING)) {
                abort(422, 'Order cannot be paid.');
            }

            $this->inventoryService->deductForPaidOrder($order, $request->user()->id);

            $payment = $order->payments()->create([
                'amount' => $data['amount_paid'],
                'method' => 'CASH',
                'processed_by' => $request->user()->id,
            ]);

            $order->update(['status' => OrderStatus::CONFIRMED]);

            return response()->json($payment, 201);
        });
    }

}
