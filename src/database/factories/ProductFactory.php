<?php

namespace Database\Factories;

use App\Enums\UnitOfMeasurement;
use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Realistic restaurant inventory items
        $products = [
            // Vegetables
            ['name' => 'Tomatoes', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 20, 'max' => 100],
            ['name' => 'Lettuce', 'unit' => UnitOfMeasurement::PIECES, 'min' => 30, 'max' => 80],
            ['name' => 'Onions', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 15, 'max' => 60],
            ['name' => 'Potatoes', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 50, 'max' => 150],
            ['name' => 'Carrots', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 20, 'max' => 70],
            ['name' => 'Bell Peppers', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 40],
            ['name' => 'Mushrooms', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 30],
            ['name' => 'Garlic', 'unit' => UnitOfMeasurement::GRAM, 'min' => 500, 'max' => 2000],
            ['name' => 'Ginger', 'unit' => UnitOfMeasurement::GRAM, 'min' => 300, 'max' => 1500],

            // Proteins
            ['name' => 'Chicken Breast', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 30, 'max' => 100],
            ['name' => 'Ground Beef', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 25, 'max' => 80],
            ['name' => 'Pork Chops', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 20, 'max' => 60],
            ['name' => 'Salmon Fillet', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 15, 'max' => 50],
            ['name' => 'Shrimp', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 40],
            ['name' => 'Bacon', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 30],
            ['name' => 'Sausages', 'unit' => UnitOfMeasurement::PACK, 'min' => 15, 'max' => 50],

            // Dairy
            ['name' => 'Whole Milk', 'unit' => UnitOfMeasurement::LITER, 'min' => 30, 'max' => 100],
            ['name' => 'Cheddar Cheese', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 40],
            ['name' => 'Mozzarella Cheese', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 12, 'max' => 45],
            ['name' => 'Butter', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 25],
            ['name' => 'Heavy Cream', 'unit' => UnitOfMeasurement::LITER, 'min' => 15, 'max' => 50],
            ['name' => 'Eggs', 'unit' => UnitOfMeasurement::DOZEN, 'min' => 20, 'max' => 80],
            ['name' => 'Yogurt', 'unit' => UnitOfMeasurement::LITER, 'min' => 10, 'max' => 40],

            // Pantry Items
            ['name' => 'All-Purpose Flour', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 40, 'max' => 120],
            ['name' => 'White Rice', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 50, 'max' => 150],
            ['name' => 'Pasta (Spaghetti)', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 25, 'max' => 80],
            ['name' => 'Olive Oil', 'unit' => UnitOfMeasurement::LITER, 'min' => 10, 'max' => 40],
            ['name' => 'Vegetable Oil', 'unit' => UnitOfMeasurement::LITER, 'min' => 15, 'max' => 60],
            ['name' => 'Sugar', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 20, 'max' => 70],
            ['name' => 'Salt', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 30],
            ['name' => 'Black Pepper', 'unit' => UnitOfMeasurement::GRAM, 'min' => 200, 'max' => 1000],

            // Condiments & Sauces
            ['name' => 'Soy Sauce', 'unit' => UnitOfMeasurement::LITER, 'min' => 8, 'max' => 25],
            ['name' => 'Ketchup', 'unit' => UnitOfMeasurement::LITER, 'min' => 5, 'max' => 20],
            ['name' => 'Mayonnaise', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 35],
            ['name' => 'Tomato Sauce', 'unit' => UnitOfMeasurement::LITER, 'min' => 15, 'max' => 50],
            ['name' => 'Vinegar', 'unit' => UnitOfMeasurement::LITER, 'min' => 8, 'max' => 30],

            // Beverages
            ['name' => 'Coffee Beans', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 20],
            ['name' => 'Orange Juice', 'unit' => UnitOfMeasurement::LITER, 'min' => 20, 'max' => 60],
            ['name' => 'Coca-Cola', 'unit' => UnitOfMeasurement::LITER, 'min' => 50, 'max' => 150],
            ['name' => 'Bottled Water', 'unit' => UnitOfMeasurement::LITER, 'min' => 100, 'max' => 300],

            // Bread & Bakery
            ['name' => 'Bread Loaves', 'unit' => UnitOfMeasurement::PIECES, 'min' => 20, 'max' => 60],
            ['name' => 'Burger Buns', 'unit' => UnitOfMeasurement::PACK, 'min' => 15, 'max' => 50],
            ['name' => 'Tortillas', 'unit' => UnitOfMeasurement::PACK, 'min' => 10, 'max' => 40],
        ];

        $product = $this->faker->randomElement($products);
        $initialStock = $this->faker->numberBetween($product['min'], $product['max']);

        // Calculate stock out (0 to 70% of initial stock)
        $stockOut = $this->faker->numberBetween(0, (int) ($initialStock * 0.7));
        $remainingStock = $initialStock - $stockOut;

        // Determine status based on remaining stock
        // Low stock threshold: 20% of initial stock
        $lowStockThreshold = (int) ($initialStock * 0.2);

        if ($remainingStock == 0) {
            $status = InventoryStatus::NoStock;
        } elseif ($remainingStock <= $lowStockThreshold) {
            $status = InventoryStatus::LowOnStock;
        } else {
            $status = InventoryStatus::OnStock;
        }

        return [
            'name' => $product['name'],
            'initial_stock' => $initialStock,
            'unit_of_measurement' => $product['unit'],
            'status' => $status,
            'stock_out' => $stockOut,
            'remaining_stock' => $remainingStock,
            'expiration_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the product is in stock.
     */
    public function inStock(): static
    {
        return $this->state(function (array $attributes) {
            $stockOut = $this->faker->numberBetween(0, (int) ($attributes['initial_stock'] * 0.3));
            $remainingStock = $attributes['initial_stock'] - $stockOut;

            return [
                'status' => InventoryStatus::OnStock,
                'stock_out' => $stockOut,
                'remaining_stock' => $remainingStock,
            ];
        });
    }

    /**
     * Indicate that the product is low on stock.
     */
    public function lowOnStock(): static
    {
        return $this->state(function (array $attributes) {
            $lowStockThreshold = (int) ($attributes['initial_stock'] * 0.2);
            $stockOut = $attributes['initial_stock'] - $this->faker->numberBetween(1, $lowStockThreshold);
            $remainingStock = $attributes['initial_stock'] - $stockOut;

            return [
                'status' => InventoryStatus::LowOnStock,
                'stock_out' => $stockOut,
                'remaining_stock' => $remainingStock,
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => InventoryStatus::NoStock,
                'stock_out' => $attributes['initial_stock'],
                'remaining_stock' => 0,
            ];
        });
    }
}