<?php

namespace Database\Factories;

use App\Enums\InventoryStatus;
use App\Enums\UnitOfMeasurement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $products = [
            // Vegetables
            ['name' => 'Tomatoes', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 20, 'max' => 50],
            ['name' => 'Lettuce', 'unit' => UnitOfMeasurement::PIECES, 'min' => 15, 'max' => 30],
            ['name' => 'Onions', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 15, 'max' => 40],
            ['name' => 'Potatoes', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 25, 'max' => 60],
            ['name' => 'Carrots', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 30],
            ['name' => 'Bell Peppers', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 20],
            ['name' => 'Mushrooms', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 15],
            ['name' => 'Garlic', 'unit' => UnitOfMeasurement::GRAM, 'min' => 500, 'max' => 2000],
            ['name' => 'Ginger', 'unit' => UnitOfMeasurement::GRAM, 'min' => 300, 'max' => 1000],
            ['name' => 'Eggplant', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 25],
            ['name' => 'Calamansi', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 15],

            // Proteins
            ['name' => 'Chicken Breast', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 15, 'max' => 40],
            ['name' => 'Ground Pork', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 12, 'max' => 35],
            ['name' => 'Pork Chops', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 30],
            ['name' => 'Ground Beef', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 25],
            ['name' => 'Beef Cubes', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 20],
            ['name' => 'Shrimp', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 20],
            ['name' => 'Squid', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 6, 'max' => 15],
            ['name' => 'Bangus (Milkfish)', 'unit' => UnitOfMeasurement::PIECES, 'min' => 10, 'max' => 25],
            ['name' => 'Pork Belly', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 20],
            ['name' => 'Bacon', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 15],
            ['name' => 'Sausages', 'unit' => UnitOfMeasurement::PACK, 'min' => 10, 'max' => 30],

            // Dairy
            ['name' => 'Milk', 'unit' => UnitOfMeasurement::LITER, 'min' => 15, 'max' => 40],
            ['name' => 'Cheddar Cheese', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 15],
            ['name' => 'Mozzarella Cheese', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 3, 'max' => 10],
            ['name' => 'Butter', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 3, 'max' => 10],
            ['name' => 'Heavy Cream', 'unit' => UnitOfMeasurement::LITER, 'min' => 5, 'max' => 15],
            ['name' => 'Eggs', 'unit' => UnitOfMeasurement::DOZEN, 'min' => 10, 'max' => 30],
            ['name' => 'Condensed Milk', 'unit' => UnitOfMeasurement::LITER, 'min' => 8, 'max' => 20],
            ['name' => 'Cream Cheese', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 4, 'max' => 12],

            // Pantry Items
            ['name' => 'Flour', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 25, 'max' => 60],
            ['name' => 'Rice', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 50, 'max' => 100],
            ['name' => 'Rice Noodles', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 25],
            ['name' => 'Pasta', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 15, 'max' => 35],
            ['name' => 'Olive Oil', 'unit' => UnitOfMeasurement::LITER, 'min' => 5, 'max' => 15],
            ['name' => 'Vegetable Oil', 'unit' => UnitOfMeasurement::LITER, 'min' => 10, 'max' => 30],
            ['name' => 'Sugar', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 20, 'max' => 50],
            ['name' => 'Brown Sugar', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 8, 'max' => 20],
            ['name' => 'Salt', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 25],
            ['name' => 'Black Pepper', 'unit' => UnitOfMeasurement::GRAM, 'min' => 300, 'max' => 1000],
            ['name' => 'Peanut Butter', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 15],

            // Condiments & Sauces
            ['name' => 'Soy Sauce', 'unit' => UnitOfMeasurement::LITER, 'min' => 8, 'max' => 20],
            ['name' => 'Vinegar', 'unit' => UnitOfMeasurement::LITER, 'min' => 8, 'max' => 20],
            ['name' => 'Ketchup', 'unit' => UnitOfMeasurement::LITER, 'min' => 6, 'max' => 15],
            ['name' => 'Mayonnaise', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 12],
            ['name' => 'Tomato Sauce', 'unit' => UnitOfMeasurement::LITER, 'min' => 10, 'max' => 25],
            ['name' => 'Oyster Sauce', 'unit' => UnitOfMeasurement::LITER, 'min' => 4, 'max' => 10],
            ['name' => 'Fish Sauce', 'unit' => UnitOfMeasurement::LITER, 'min' => 4, 'max' => 10],

            // Beverages
            ['name' => 'Coffee Beans', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 5, 'max' => 15],
            ['name' => 'Orange Juice', 'unit' => UnitOfMeasurement::LITER, 'min' => 10, 'max' => 25],
            ['name' => 'Coca-Cola', 'unit' => UnitOfMeasurement::LITER, 'min' => 20, 'max' => 50],
            ['name' => 'Bottled Water', 'unit' => UnitOfMeasurement::LITER, 'min' => 50, 'max' => 100],

            // Bread & Pastry
            ['name' => 'Bread Loaves', 'unit' => UnitOfMeasurement::PIECES, 'min' => 20, 'max' => 50],
            ['name' => 'Burger Buns', 'unit' => UnitOfMeasurement::PACK, 'min' => 10, 'max' => 30],
            ['name' => 'Spring Roll Wrappers', 'unit' => UnitOfMeasurement::PACK, 'min' => 8, 'max' => 20],

            // Filipino Specific
            ['name' => 'Ube Halaya', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 3, 'max' => 10],
            ['name' => 'Banana', 'unit' => UnitOfMeasurement::PIECES, 'min' => 30, 'max' => 80],
            ['name' => 'Mango', 'unit' => UnitOfMeasurement::KILOGRAM, 'min' => 10, 'max' => 30],
            ['name' => 'Young Coconut', 'unit' => UnitOfMeasurement::PIECES, 'min' => 15, 'max' => 40],
            ['name' => 'Ice Cream', 'unit' => UnitOfMeasurement::LITER, 'min' => 8, 'max' => 20],
        ];

        static $index = 0;
        $product = $products[$index % count($products)];
        $index++;

        $initialStock = rand($product['min'], $product['max']);
        $stockOutPercentage = rand(0, 70) / 100;
        $stockOut = (int) ($initialStock * $stockOutPercentage);
        $remainingStock = $initialStock - $stockOut;

        $status = match(true) {
            $remainingStock == 0 => InventoryStatus::NoStock,
            $remainingStock <= ($initialStock * 0.2) => InventoryStatus::LowOnStock,
            default => InventoryStatus::OnStock,
        };

        return [
            'name' => $product['name'],
            'initial_stock' => $initialStock,
            'unit_of_measurement' => $product['unit'],
            'status' => $status,
            'stock_out' => $stockOut,
            'remaining_stock' => $remainingStock,
            'expiration_date' => fake()->dateTimeBetween('now', '+6 months'),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InventoryStatus::OnStock,
            'remaining_stock' => (int) ($attributes['initial_stock'] * rand(60, 100) / 100),
        ]);
    }

    public function lowOnStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InventoryStatus::LowOnStock,
            'remaining_stock' => (int) ($attributes['initial_stock'] * rand(5, 20) / 100),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InventoryStatus::NoStock,
            'remaining_stock' => 0,
        ]);
    }
}