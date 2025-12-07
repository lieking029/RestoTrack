<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all menu items (22 dishes)
        $menus = Menu::factory()->count(22)->create();

        // Map ingredients to dishes
        $menuIngredients = [
            'Lumpia Shanghai' => [
                'Ground Pork' => 0.3,      // 300g
                'Onions' => 0.1,           // 100g
                'Carrots' => 0.1,          // 100g
                'Garlic' => 20,            // 20g
            ],
            'Crispy Calamares' => [
                'Squid' => 0.5,            // 500g
                'Flour' => 0.2,            // 200g
            ],
            'Cheese Sticks' => [
                'Cheddar Cheese' => 0.3,   // 300g
                'Spring Roll Wrappers' => 1, // 1 pack
            ],
            'Fresh Garden Salad' => [
                'Lettuce' => 0.2,          // 200g
                'Tomatoes' => 0.15,        // 150g
                'Bell Peppers' => 0.1,     // 100g
            ],
            'Sinigang na Baboy' => [
                'Pork Chops' => 0.5,       // 500g
                'Tomatoes' => 0.2,         // 200g
                'Onions' => 0.1,           // 100g
            ],
            'Chicken Adobo' => [
                'Chicken Breast' => 0.6,   // 600g
                'Soy Sauce' => 0.1,        // 100ml
                'Vinegar' => 0.1,          // 100ml
                'Garlic' => 30,            // 30g
            ],
            'Kare-Kare' => [
                'Beef' => 0.6,             // 600g
                'Peanut Butter' => 0.2,    // 200g
            ],
            'Grilled Bangus' => [
                'Bangus' => 1,             // 1 whole fish
                'Tomatoes' => 0.1,         // 100g
                'Onions' => 0.05,          // 50g
            ],
            'Lechon Kawali' => [
                'Pork Belly' => 0.7,       // 700g
            ],
            'Beef Caldereta' => [
                'Beef' => 0.5,             // 500g
                'Tomato Sauce' => 0.2,     // 200ml
                'Bell Peppers' => 0.15,    // 150g
                'Potatoes' => 0.2,         // 200g
            ],
            'Pinakbet' => [
                'Pork' => 0.2,             // 200g
                'Tomatoes' => 0.15,        // 150g
                'Eggplant' => 0.2,         // 200g
            ],
            'Crispy Pata' => [
                'Pork Knuckle' => 1.2,     // 1.2kg
            ],
            'Halo-Halo' => [
                'Milk' => 0.2,             // 200ml
                'Sugar' => 0.05,           // 50g
                'Ice Cream' => 1,          // 1 scoop
            ],
            'Leche Flan' => [
                'Eggs' => 6,               // 6 eggs (half dozen)
                'Condensed Milk' => 0.4,   // 400ml
                'Sugar' => 0.1,            // 100g
            ],
            'Ube Cheesecake' => [
                'Cream Cheese' => 0.4,     // 400g
                'Sugar' => 0.15,           // 150g
                'Ube Halaya' => 0.2,       // 200g
            ],
            'Turon' => [
                'Bananas' => 3,            // 3 pieces
                'Spring Roll Wrappers' => 1, // 1 pack
                'Brown Sugar' => 0.1,      // 100g
            ],
            'Calamansi Juice' => [
                'Calamansi' => 0.15,       // 150g
                'Sugar' => 0.05,           // 50g
            ],
            'Mango Shake' => [
                'Mango' => 0.3,            // 300g
                'Milk' => 0.25,            // 250ml
                'Sugar' => 0.03,           // 30g
            ],
            'Iced Coffee' => [
                'Coffee Beans' => 0.03,    // 30g
                'Sugar' => 0.02,           // 20g
                'Milk' => 0.05,            // 50ml (optional)
            ],
            'Buko Juice' => [
                'Young Coconut' => 1,      // 1 piece
            ],
            'Palabok' => [
                'Rice Noodles' => 0.2,     // 200g
                'Shrimp' => 0.15,          // 150g
                'Eggs' => 2,               // 2 eggs
            ],
            'Chicken Empanada' => [
                'Chicken Breast' => 0.2,   // 200g
                'Potatoes' => 0.15,        // 150g
                'Carrots' => 0.05,         // 50g
            ],
        ];

        // Attach ingredients to each menu
        foreach ($menus as $menu) {
            if (isset($menuIngredients[$menu->name])) {
                $ingredients = $menuIngredients[$menu->name];

                foreach ($ingredients as $productName => $quantity) {
                    // Find the product by name (case-insensitive, partial match)
                    $product = Product::where('name', 'LIKE', "%{$productName}%")->first();

                    if ($product) {
                        // Attach product with quantity needed
                        $menu->products()->attach($product->id, [
                            'quantity_needed' => $quantity,
                        ]);
                    }
                }
            }
        }

        // Make some items unavailable (out of stock ingredients)
        $unavailableCount = rand(2, 4);
        Menu::inRandomOrder()
            ->limit($unavailableCount)
            ->update(['status' => \App\Enums\MenuStatus::Unavailable]);

        $this->command->info('✓ Created 22 menu items with ingredients');
        $this->command->info('✓ Available: ' . Menu::where('status', \App\Enums\MenuStatus::Available)->count());
        $this->command->info('✓ Unavailable: ' . Menu::where('status', \App\Enums\MenuStatus::Unavailable)->count());
    }
}