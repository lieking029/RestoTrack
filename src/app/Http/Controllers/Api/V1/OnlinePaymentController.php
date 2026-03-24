<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OnlinePaymentController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $order = Order::with('items')->findOrFail($data['order_id']);

        if (!$order->status->is(OrderStatus::PENDING)) {
            abort(422, 'Order cannot be paid.');
        }

        $lineItems = $order->items->map(function ($item) {
            return [
                'currency' => 'PHP',
                'amount' => (int) round($item->unit_price * $item->quantity * 100),
                'name' => $item->name,
                'quantity' => 1,
                'description' => $item->name . ' x' . $item->quantity,
            ];
        })->values()->toArray();

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
                    'payment_method_types' => ['gcash', 'card', 'paymaya', 'grab_pay'],
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
        ]);
    }

    public function checkStatus(Order $order)
    {
        return response()->json([
            'status' => $order->status->value,
            'is_paid' => $order->status->is(OrderStatus::CONFIRMED),
        ]);
    }
}
