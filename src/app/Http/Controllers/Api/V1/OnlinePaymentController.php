<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OnlinePaymentController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'discount_type' => ['nullable', 'in:PWD,SENIOR'],
            'customer_name' => ['required_with:discount_type', 'nullable', 'string', 'max:255'],
            'id_number' => ['required_with:discount_type', 'nullable', 'string', 'max:100'],
        ]);

        $order = Order::with('items')->findOrFail($data['order_id']);

        if (!$order->status->is(OrderStatus::SERVED)) {
            abort(422, 'Order must be served before payment.');
        }

        // Apply PWD/Senior discount: 20% off subtotal
        $discountAmount = 0;
        $payableTotal = $order->total;

        if (!empty($data['discount_type'])) {
            $discountAmount = round($order->subtotal * 0.20, 2);
            $payableTotal = round($order->subtotal - $discountAmount, 2);

            $order->update([
                'discount_type' => $data['discount_type'],
                'customer_name' => $data['customer_name'],
                'id_number' => $data['id_number'],
                'discount_amount' => $discountAmount,
                'discount_total' => $payableTotal,
            ]);
        }

        $lineItems = [];

        if (!empty($data['discount_type'])) {
            // Send as a single line item with the discounted total
            $lineItems[] = [
                'currency' => 'PHP',
                'amount' => (int) round($payableTotal * 100),
                'name' => 'Order #' . $order->id,
                'quantity' => 1,
                'description' => 'Order total with ' . $data['discount_type'] . ' discount (20% off)',
            ];
        } else {
            // No discount — send individual items
            $lineItems = $order->items->map(function ($item) {
                return [
                    'currency' => 'PHP',
                    'amount' => (int) round($item->unit_price * $item->quantity * 100),
                    'name' => $item->name,
                    'quantity' => 1,
                    'description' => $item->name . ' x' . $item->quantity,
                ];
            })->values()->toArray();
        }

        $url = config('services.paymongo.url');
        $secretKey = config('services.paymongo.secret_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
        ])->post($url . '/checkout_sessions', [
            'data' => [
                'attributes' => [
                    'line_items' => $lineItems,
                    'payment_method_types' => ['gcash', 'card', 'paymaya', 'grab_pay', 'qrph'],
                    'description' => 'Order #' . $order->id,
                    'reference_number' => (string) $order->id,
                    'success_url' => config('app.url') . '/payment/success?order_id=' . $order->id,
                    'cancel_url' => config('app.url') . '/payment/cancel?order_id=' . $order->id,
                ],
            ],
        ]);

        if ($response->failed()) {
            abort(500, 'Failed to create checkout session.');
        }

        $checkout = $response->json();

        return response()->json([
            'checkout_url' => $checkout['data']['attributes']['checkout_url'],
            'checkout_session_id' => $checkout['data']['id'],
            'discount_type' => $data['discount_type'] ?? null,
            'discount_amount' => $discountAmount,
            'original_total' => (float) $order->total,
            'discount_total' => $payableTotal,
        ]);
    }

    public function checkStatus(Order $order)
    {
        return response()->json([
            'status' => $order->status->value,
            'is_paid' => $order->status->is(OrderStatus::COMPLETED),
        ]);
    }
}
