<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Enums\OrderStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users by role
        $servers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['server', 'barista']))->get();
        $cashiers = User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->get();
        $menus = Menu::where('status', MenuStatus::Available())->get();

        if ($servers->isEmpty() || $cashiers->isEmpty() || $menus->isEmpty()) {
            $this->command->warn('Missing required data (servers, cashiers, or menu items). Skipping orders.');
            return;
        }

        // Weighted payment methods
        $paymentMethods = ['cash', 'cash', 'cash', 'cash', 'cash', 'card', 'card', 'card', 'gcash', 'gcash', 'maya'];

        $orderCounts = [
            'pending' => 5,
            'confirmed' => 5,
            'inPreparation' => 3,
            'ready' => 5,
            'completed' => 30,
            'cancelled' => 2,
        ];

        $totalOrders = 0;
        $totalItems = 0;
        $totalPayments = 0;

        // Create COMPLETED orders (historical, with payments)
        for ($i = 0; $i < $orderCounts['completed']; $i++) {
            $order = $this->createOrderWithItems(
                $servers->random(),
                $cashiers->random(),
                $menus,
                OrderStatus::COMPLETED(),
                fake()->dateTimeBetween('-30 days', '-2 hours')
            );
            $totalOrders++;
            $totalItems += $order->items->count();

            // Create payment for completed orders
            Payment::create([
                'order_id' => $order->id,
                'processed_by' => $order->processed_by,
                'amount' => $order->total,
                'method' => fake()->randomElement($paymentMethods),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
            $totalPayments++;
        }

        // Create READY orders (prepared, waiting for pickup)
        for ($i = 0; $i < $orderCounts['ready']; $i++) {
            $order = $this->createOrderWithItems(
                $servers->random(),
                $cashiers->random(),
                $menus,
                OrderStatus::READY(),
                fake()->dateTimeBetween('-30 minutes', '-5 minutes')
            );
            $totalOrders++;
            $totalItems += $order->items->count();
        }

        // Create IN_PREPARATION orders (kitchen is cooking)
        for ($i = 0; $i < $orderCounts['inPreparation']; $i++) {
            $order = $this->createOrderWithItems(
                $servers->random(),
                $cashiers->random(),
                $menus,
                OrderStatus::INPREPARATION(),
                fake()->dateTimeBetween('-45 minutes', '-15 minutes')
            );
            $totalOrders++;
            $totalItems += $order->items->count();
        }

        // Create CONFIRMED orders (paid, waiting for kitchen)
        for ($i = 0; $i < $orderCounts['confirmed']; $i++) {
            $order = $this->createOrderWithItems(
                $servers->random(),
                $cashiers->random(),
                $menus,
                OrderStatus::CONFIRMED(),
                fake()->dateTimeBetween('-1 hour', '-30 minutes')
            );
            $totalOrders++;
            $totalItems += $order->items->count();
        }

        // Create PENDING orders (just created, awaiting payment)
        for ($i = 0; $i < $orderCounts['pending']; $i++) {
            $order = $this->createOrderWithItems(
                $servers->random(),
                null, // No cashier yet for pending
                $menus,
                OrderStatus::PENDING(),
                fake()->dateTimeBetween('-2 hours', 'now')
            );
            $totalOrders++;
            $totalItems += $order->items->count();
        }

        // Create CANCELLED orders
        for ($i = 0; $i < $orderCounts['cancelled']; $i++) {
            $order = $this->createOrderWithItems(
                $servers->random(),
                $cashiers->random(),
                $menus,
                OrderStatus::CANCELLED(),
                fake()->dateTimeBetween('-14 days', '-1 day')
            );
            $totalOrders++;
            $totalItems += $order->items->count();
        }

        $this->command->info("✓ Created {$totalOrders} orders with {$totalItems} items");
        $this->command->info("✓ Created {$totalPayments} payments");
        $this->command->info('  - PENDING: ' . Order::where('status', OrderStatus::PENDING())->count());
        $this->command->info('  - CONFIRMED: ' . Order::where('status', OrderStatus::CONFIRMED())->count());
        $this->command->info('  - IN PREPARATION: ' . Order::where('status', OrderStatus::INPREPARATION())->count());
        $this->command->info('  - READY: ' . Order::where('status', OrderStatus::READY())->count());
        $this->command->info('  - COMPLETED: ' . Order::where('status', OrderStatus::COMPLETED())->count());
        $this->command->info('  - CANCELLED: ' . Order::where('status', OrderStatus::CANCELLED())->count());
    }

    /**
     * Create an order with random items.
     */
    private function createOrderWithItems(
        User $creator,
        ?User $processor,
        $menus,
        OrderStatus $status,
        $createdAt
    ): Order {
        $order = Order::create([
            'created_by' => $creator->id,
            'processed_by' => $processor?->id,
            'status' => $status,
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'created_at' => $createdAt,
            'updated_at' => now(),
        ]);

        // Add 1-6 items to the order
        $itemCount = fake()->numberBetween(1, 6);
        $selectedMenus = $menus->random(min($itemCount, $menus->count()));
        $subtotal = 0;

        foreach ($selectedMenus as $menu) {
            $quantity = fake()->randomElement([1, 1, 1, 2, 2, 3]); // Weighted toward lower
            $itemTotal = $menu->price * $quantity;
            $subtotal += $itemTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'unit_price' => $menu->price,
                'quantity' => $quantity,
                'total' => $itemTotal,
            ]);
        }

        // Calculate totals (12% VAT)
        $tax = round($subtotal * 0.12, 2);
        $total = $subtotal + $tax;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        return $order->load('items');
    }
}
