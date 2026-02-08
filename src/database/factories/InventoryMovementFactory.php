<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryMovement>
 *
 * Allowed reasons: SALE, CANCEL, ADJUSTMENT, WASTE, RECEIVING
 */
class InventoryMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'inventory_item_id' => InventoryItem::inRandomOrder()->first()?->id,
            'order_id' => null,
            'performed_by' => User::whereHas('roles')->inRandomOrder()->first()?->id,
            'type' => 'CREDIT',
            'reason' => 'RECEIVING',
            'quantity' => fake()->numberBetween(10, 100),
            'note' => 'Stock received from supplier',
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Stock receiving movement (adding stock from supplier).
     */
    public function receiving(): static
    {
        $suppliers = [
            'Metro Wholesale',
            'S&R Membership',
            'Puregold',
            'Local Market',
            'Direct Supplier',
        ];

        $notes = [
            'Regular weekly delivery',
            'Emergency restock',
            'Monthly bulk order',
            'Fresh produce delivery',
            'Supplier delivery received',
        ];

        return $this->state(fn(array $attributes) => [
            'type' => 'CREDIT',
            'reason' => 'RECEIVING',
            'quantity' => fake()->numberBetween(20, 150),
            'note' => fake()->randomElement($notes) . ' from ' . fake()->randomElement($suppliers),
            'order_id' => null,
        ]);
    }

    /**
     * Waste movement (spoilage, damage, expiry).
     */
    public function waste(): static
    {
        $wasteReasons = [
            'Expired - past best before date',
            'Spoiled - improper storage',
            'Damaged during handling',
            'Quality check failed',
            'Contamination detected',
            'Freezer malfunction',
            'Pest damage',
        ];

        return $this->state(fn(array $attributes) => [
            'type' => 'DEBIT',
            'reason' => 'WASTE',
            'quantity' => fake()->numberBetween(1, 20),
            'note' => fake()->randomElement($wasteReasons),
            'order_id' => null,
        ]);
    }

    /**
     * Adjustment movement (inventory corrections).
     */
    public function adjustment(): static
    {
        $isPositive = fake()->boolean(40); // 40% chance of positive adjustment
        $adjustmentNotes = [
            'Physical count adjustment',
            'Inventory audit correction',
            'System discrepancy fix',
            'Stock take variance',
            'Missing items found',
            'Counting error correction',
        ];

        return $this->state(fn(array $attributes) => [
            'type' => $isPositive ? 'CREDIT' : 'DEBIT',
            'reason' => 'ADJUSTMENT',
            'quantity' => fake()->numberBetween(1, 15),
            'note' => fake()->randomElement($adjustmentNotes),
            'order_id' => null,
        ]);
    }

    /**
     * Sale movement (deducted for order/sale).
     */
    public function sale(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'DEBIT',
            'reason' => 'SALE',
            'quantity' => fake()->numberBetween(1, 10),
            'note' => 'Deducted for order',
        ]);
    }

    /**
     * Cancel movement (restored from cancelled order).
     */
    public function cancel(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'CREDIT',
            'reason' => 'CANCEL',
            'quantity' => fake()->numberBetween(1, 10),
            'note' => 'Restored from cancelled order',
        ]);
    }

    /**
     * Set the inventory item.
     */
    public function forInventoryItem(InventoryItem $item): static
    {
        return $this->state(fn(array $attributes) => [
            'inventory_item_id' => $item->id,
        ]);
    }

    /**
     * Set the order (for sale/cancel movements).
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn(array $attributes) => [
            'order_id' => $order->id,
            'created_at' => $order->created_at,
        ]);
    }

    /**
     * Set the performer.
     */
    public function performedBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'performed_by' => $user->id,
        ]);
    }
}
