<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Note: This seeder is typically called from OrderSeeder.
     * If run standalone, it adds items to existing orders without items.
     */
    public function run(): void
    {
        $ordersWithoutItems = Order::doesntHave('items')->get();
        $menus = Menu::where('status', MenuStatus::Available())->get();

        if ($ordersWithoutItems->isEmpty()) {
            $this->command->info('All orders already have items. Skipping.');
            return;
        }

        if ($menus->isEmpty()) {
            $this->command->warn('No available menu items found. Skipping.');
            return;
        }

        $totalItems = 0;

        foreach ($ordersWithoutItems as $order) {
            $itemCount = fake()->numberBetween(1, 6);
            $selectedMenus = $menus->random(min($itemCount, $menus->count()));
            $subtotal = 0;

            foreach ($selectedMenus as $menu) {
                $quantity = fake()->randomElement([1, 1, 1, 2, 2, 3]);
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
                $totalItems++;
            }

            // Update order totals
            $tax = round($subtotal * 0.12, 2);
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);
        }

        $this->command->info("✓ Created {$totalItems} order items for " . $ordersWithoutItems->count() . " orders");
    }
}
