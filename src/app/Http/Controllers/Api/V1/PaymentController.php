<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

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

            if (!$order->status->is(OrderStatus::SERVED)) {
                abort(422, 'Order must be served before payment.');
            }

            $payment = $order->payments()->create([
                'amount' => $data['amount_paid'],
                'method' => 'CASH',
                'processed_by' => $request->user()->id,
            ]);

            $order->update([
                'status' => OrderStatus::COMPLETED,
                'processed_by' => $request->user()->id,
            ]);

            return response()->json($payment, 201);
        });
    }

}
