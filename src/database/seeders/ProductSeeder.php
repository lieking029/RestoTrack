<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Enums\InventoryStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()
            ->count(20)
            ->inStock()
            ->create();

        Product::factory()
            ->count(10)
            ->lowOnStock()
            ->create();

        Product::factory()
            ->count(3)
            ->outOfStock()
            ->create();

        $this->command->info('Products seeded successfully!');
        $this->command->newLine();
        $this->command->info('Total products: ' . Product::count());
        $this->command->info('In Stock: ' . Product::where('status', InventoryStatus::OnStock)->count());
        $this->command->info('Low on Stock: ' . Product::where('status', InventoryStatus::LowOnStock)->count());
        $this->command->info('Out of Stock: ' . Product::where('status', InventoryStatus::NoStock)->count());
    }
}