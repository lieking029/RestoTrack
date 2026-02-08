<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Note: This seeder is typically called from OrderSeeder.
     * If run standalone, it creates payments for completed orders without payments.
     */
    public function run(): void
    {
        // Find completed orders without payments
        $ordersWithoutPayments = Order::where('status', OrderStatus::COMPLETED())
            ->doesntHave('payments')
            ->get();

        $cashiers = User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->get();

        if ($ordersWithoutPayments->isEmpty()) {
            $this->command->info('All completed orders already have payments. Skipping.');
            return;
        }

        if ($cashiers->isEmpty()) {
            $this->command->warn('No cashiers found. Skipping payments.');
            return;
        }

        // Weighted payment methods
        $paymentMethods = [
            'cash', 'cash', 'cash', 'cash', 'cash',  // 50%
            'card', 'card', 'card',                   // 25%
            'gcash', 'gcash',                         // 15%
            'maya',                                   // 10%
        ];

        $totalPayments = 0;

        foreach ($ordersWithoutPayments as $order) {
            Payment::create([
                'order_id' => $order->id,
                'processed_by' => $order->processed_by ?? $cashiers->random()->id,
                'amount' => $order->total,
                'method' => fake()->randomElement($paymentMethods),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
            $totalPayments++;
        }

        $this->command->info("✓ Created {$totalPayments} payments");

        // Payment method distribution
        $methodCounts = Payment::selectRaw('method, COUNT(*) as count')
            ->groupBy('method')
            ->pluck('count', 'method')
            ->toArray();

        foreach ($methodCounts as $method => $count) {
            $this->command->info("  - {$method}: {$count}");
        }
    }
}
