<?php

namespace Database\Factories;

use App\Enums\MenuStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $menu = Menu::where('status', MenuStatus::Available())->inRandomOrder()->first();
        $quantity = fake()->randomElement([1, 1, 1, 2, 2, 3]); // Weighted toward lower quantities

        return [
            'order_id' => Order::factory(),
            'menu_id' => $menu?->id,
            'name' => $menu?->name ?? 'Menu Item',
            'unit_price' => $menu?->price ?? fake()->randomFloat(2, 80, 550),
            'quantity' => $quantity,
            'total' => ($menu?->price ?? 150) * $quantity,
        ];
    }

    /**
     * Set the parent order.
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn(array $attributes) => [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Set specific menu item.
     */
    public function forMenu(Menu $menu): static
    {
        $quantity = fake()->randomElement([1, 1, 1, 2, 2, 3]);

        return $this->state(fn(array $attributes) => [
            'menu_id' => $menu->id,
            'name' => $menu->name,
            'unit_price' => $menu->price,
            'quantity' => $quantity,
            'total' => $menu->price * $quantity,
        ]);
    }

    /**
     * Set specific quantity.
     */
    public function quantity(int $quantity): static
    {
        return $this->state(fn(array $attributes) => [
            'quantity' => $quantity,
            'total' => $attributes['unit_price'] * $quantity,
        ]);
    }
}
