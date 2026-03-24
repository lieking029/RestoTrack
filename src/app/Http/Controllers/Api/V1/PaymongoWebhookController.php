<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymongoWebhookController extends Controller
{

    public function handle(Request $request)
    {
        Log::info('PayMongo webhook received', ['payload' => $request->all()]);

        $event = $request->input('data.attributes');

        if ($event['type'] !== 'checkout_session.payment.paid') {
            return response()->json(['message' => 'ignored'], 200);
        }

        $paymentData = $event['data']['attributes'];
        $orderId = $paymentData['reference_number'] ?? null;

        if (!$orderId) {
            Log::warning('PayMongo webhook: missing reference_number', $event);
            return response()->json(['message' => 'missing reference'], 400);
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::warning('PayMongo webhook: order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'order not found'], 404);
        }

        if (!$order->status->is(OrderStatus::SERVED)) {
            return response()->json(['message' => 'order not ready for payment'], 200);
        }

        DB::transaction(function () use ($order, $paymentData) {
            $paymentMethod = $paymentData['payment_method_used'] ?? 'ONLINE';
            $amount = $paymentData['payment_intent']['attributes']['amount'] ?? 0;

            $order->payments()->create([
                'amount' => $amount / 100,
                'method' => strtoupper($paymentMethod),
                'processed_by' => $order->created_by,
            ]);

            $order->update(['status' => OrderStatus::COMPLETED]);
        });

        Log::info('PayMongo webhook: order paid', ['order_id' => $orderId]);

        return response()->json(['message' => 'ok'], 200);
    }
}
