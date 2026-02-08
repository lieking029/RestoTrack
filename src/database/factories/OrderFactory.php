<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 150, 2500);
        $tax = round($subtotal * 0.12, 2);

        return [
            'created_by' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['server', 'barista']))->inRandomOrder()->first()?->id,
            'processed_by' => null,
            'status' => OrderStatus::PENDING(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Order is pending (just created, awaiting payment).
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::PENDING(),
            'processed_by' => null,
            'created_at' => fake()->dateTimeBetween('-2 hours', 'now'),
        ]);
    }

    /**
     * Order is confirmed (paid, waiting for kitchen).
     */
    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::CONFIRMED(),
            'processed_by' => User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Order is in preparation (kitchen is cooking).
     */
    public function inPreparation(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::INPREPARATION(),
            'processed_by' => User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-45 minutes', '-15 minutes'),
        ]);
    }

    /**
     * Order is ready (finished cooking, waiting for pickup).
     */
    public function ready(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::READY(),
            'processed_by' => User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-30 minutes', '-5 minutes'),
        ]);
    }

    /**
     * Order is completed (delivered/picked up).
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::COMPLETED(),
            'processed_by' => User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-30 days', '-1 hour'),
        ]);
    }

    /**
     * Order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrderStatus::CANCELLED(),
            'processed_by' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['cashier', 'manager']))->inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-14 days', '-1 day'),
        ]);
    }

    /**
     * Set the creator of the order.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Set the processor (cashier) of the order.
     */
    public function processedBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'processed_by' => $user->id,
        ]);
    }
}
