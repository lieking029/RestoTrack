<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Weighted payment methods (realistic distribution)
        $paymentMethods = [
            'cash', 'cash', 'cash', 'cash', 'cash',  // 50%
            'card', 'card', 'card',                   // 25% (adjusted)
            'gcash', 'gcash',                         // 15% (adjusted)
            'maya',                                   // 10%
        ];

        return [
            'order_id' => Order::factory()->completed(),
            'processed_by' => User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->inRandomOrder()->first()?->id,
            'amount' => fake()->randomFloat(2, 150, 3000),
            'method' => fake()->randomElement($paymentMethods),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Set the parent order.
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn(array $attributes) => [
            'order_id' => $order->id,
            'amount' => $order->total,
            'created_at' => $order->created_at,
        ]);
    }

    /**
     * Set payment as cash.
     */
    public function cash(): static
    {
        return $this->state(fn(array $attributes) => [
            'method' => 'cash',
        ]);
    }

    /**
     * Set payment as card.
     */
    public function card(): static
    {
        return $this->state(fn(array $attributes) => [
            'method' => 'card',
        ]);
    }

    /**
     * Set payment as GCash.
     */
    public function gcash(): static
    {
        return $this->state(fn(array $attributes) => [
            'method' => 'gcash',
        ]);
    }

    /**
     * Set payment as Maya.
     */
    public function maya(): static
    {
        return $this->state(fn(array $attributes) => [
            'method' => 'maya',
        ]);
    }

    /**
     * Set the cashier who processed the payment.
     */
    public function processedBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'processed_by' => $user->id,
        ]);
    }
}
