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
            'discount_type' => ['nullable', 'in:PWD,SENIOR'],
            'customer_name' => ['required_with:discount_type', 'nullable', 'string', 'max:255'],
            'id_number' => ['required_with:discount_type', 'nullable', 'string', 'max:100'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $order = Order::lockForUpdate()->findOrFail($data['order_id']);

            $this->authorize('pay', $order);

            if (!$order->status->is(OrderStatus::SERVED)) {
                abort(422, 'Order must be served before payment.');
            }

            $discountAmount = 0;
            $discountTotal = $order->total;

            // Apply PWD/Senior discount: 20% off subtotal + VAT exempt
            if (!empty($data['discount_type'])) {
                $discountAmount = round($order->subtotal * 0.20, 2);
                $discountTotal = round($order->subtotal - $discountAmount, 2); // No tax (VAT exempt)

                $order->update([
                    'discount_type' => $data['discount_type'],
                    'customer_name' => $data['customer_name'],
                    'id_number' => $data['id_number'],
                    'discount_amount' => $discountAmount,
                    'discount_total' => $discountTotal,
                ]);
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

            return response()->json([
                'payment' => $payment,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_amount' => $discountAmount,
                'original_total' => (float) $order->total,
                'discount_total' => $discountTotal,
            ], 201);
        });
    }

}
