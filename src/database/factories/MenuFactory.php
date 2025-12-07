<?php

namespace Database\Factories;

use App\Enums\MenuType;
use App\Enums\MenuStatus;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dishes = [
            // Appetizers
            [
                'name' => 'Lumpia Shanghai',
                'description' => 'Crispy Filipino spring rolls filled with seasoned ground pork and vegetables, served with sweet chili sauce',
                'price' => 180.00,
                'category' => MenuType::Appetizer,
                'image' => 'menu/lumpia-shanghai.jpg',
            ],
            [
                'name' => 'Crispy Calamares',
                'description' => 'Golden fried squid rings with crispy coating, served with garlic mayo dip',
                'price' => 220.00,
                'category' => MenuType::Appetizer,
                'image' => 'menu/calamares.jpg',
            ],
            [
                'name' => 'Cheese Sticks',
                'description' => 'Crunchy spring rolls filled with creamy cheese, perfect starter for the family',
                'price' => 150.00,
                'category' => MenuType::Appetizer,
                'image' => 'menu/cheese-sticks.jpg',
            ],
            [
                'name' => 'Fresh Garden Salad',
                'description' => 'Mixed greens with cherry tomatoes, cucumber, and house vinaigrette',
                'price' => 160.00,
                'category' => MenuType::Appetizer,
                'image' => 'menu/garden-salad.jpg',
            ],

            // Main Course
            [
                'name' => 'Sinigang na Baboy',
                'description' => 'Traditional Filipino tamarind soup with tender pork ribs, vegetables, and tangy broth',
                'price' => 350.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/sinigang.jpg',
            ],
            [
                'name' => 'Chicken Adobo',
                'description' => 'Philippines\' national dish - chicken braised in soy sauce, vinegar, and garlic',
                'price' => 280.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/adobo.jpg',
            ],
            [
                'name' => 'Kare-Kare',
                'description' => 'Savory peanut stew with oxtail, tripe, and vegetables, served with bagoong',
                'price' => 420.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/kare-kare.jpg',
            ],
            [
                'name' => 'Grilled Bangus',
                'description' => 'Whole marinated milkfish grilled to perfection, served with tomato salsa',
                'price' => 320.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/bangus.jpg',
            ],
            [
                'name' => 'Lechon Kawali',
                'description' => 'Crispy deep-fried pork belly with tender meat, served with lechon sauce',
                'price' => 380.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/lechon-kawali.jpg',
            ],
            [
                'name' => 'Beef Caldereta',
                'description' => 'Rich tomato-based beef stew with bell peppers, potatoes, and liver spread',
                'price' => 390.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/caldereta.jpg',
            ],
            [
                'name' => 'Pinakbet',
                'description' => 'Mixed vegetables cooked in shrimp paste with pork, a healthy Filipino favorite',
                'price' => 240.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/pinakbet.jpg',
            ],
            [
                'name' => 'Crispy Pata',
                'description' => 'Deep-fried pork knuckle with crispy skin and tender meat, served with soy-vinegar dip',
                'price' => 550.00,
                'category' => MenuType::MainCourse,
                'image' => 'menu/crispy-pata.jpg',
            ],

            // Desserts
            [
                'name' => 'Halo-Halo',
                'description' => 'Classic Filipino shaved ice dessert with mixed fruits, beans, leche flan, and ube ice cream',
                'price' => 150.00,
                'category' => MenuType::Dessert,
                'image' => 'menu/halo-halo.jpg',
            ],
            [
                'name' => 'Leche Flan',
                'description' => 'Creamy caramel custard, rich and silky smooth',
                'price' => 120.00,
                'category' => MenuType::Dessert,
                'image' => 'menu/leche-flan.jpg',
            ],
            [
                'name' => 'Ube Cheesecake',
                'description' => 'Purple yam cheesecake with graham crust, a modern Filipino twist',
                'price' => 180.00,
                'category' => MenuType::Dessert,
                'image' => 'menu/ube-cheesecake.jpg',
            ],
            [
                'name' => 'Turon',
                'description' => 'Fried banana spring rolls with jackfruit and caramelized sugar coating',
                'price' => 100.00,
                'category' => MenuType::Dessert,
                'image' => 'menu/turon.jpg',
            ],

            // Beverages
            [
                'name' => 'Calamansi Juice',
                'description' => 'Freshly squeezed Philippine lime juice, sweet and tangy',
                'price' => 80.00,
                'category' => MenuType::Beverage,
                'image' => 'menu/calamansi.jpg',
            ],
            [
                'name' => 'Mango Shake',
                'description' => 'Thick and creamy shake made with fresh Philippine mangoes',
                'price' => 120.00,
                'category' => MenuType::Beverage,
                'image' => 'menu/mango-shake.jpg',
            ],
            [
                'name' => 'Iced Coffee',
                'description' => 'Strong brewed coffee over ice, sweetened to perfection',
                'price' => 90.00,
                'category' => MenuType::Beverage,
                'image' => 'menu/iced-coffee.jpg',
            ],
            [
                'name' => 'Buko Juice',
                'description' => 'Fresh young coconut water with tender meat, naturally sweet',
                'price' => 100.00,
                'category' => MenuType::Beverage,
                'image' => 'menu/buko-juice.jpg',
            ],

            // Snacks
            [
                'name' => 'Palabok',
                'description' => 'Rice noodles topped with shrimp sauce, eggs, chicharron, and tinapa flakes',
                'price' => 180.00,
                'category' => MenuType::Snack,
                'image' => 'menu/palabok.jpg',
            ],
            [
                'name' => 'Chicken Empanada',
                'description' => 'Flaky pastry filled with seasoned chicken, potatoes, and vegetables',
                'price' => 85.00,
                'category' => MenuType::Snack,
                'image' => 'menu/empanada.jpg',
            ],
        ];

        static $index = 0;
        $dish = $dishes[$index % count($dishes)];
        $index++;

        return [
            'dish_picture' => $dish['image'],
            'name' => $dish['name'],
            'description' => $dish['description'],
            'price' => $dish['price'],
            'category' => $dish['category'],
            'status' => MenuStatus::Available,
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the menu item is unavailable.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MenuStatus::Unavailable,
        ]);
    }

    /**
     * Indicate that the menu item is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MenuStatus::Available,
        ]);
    }
}