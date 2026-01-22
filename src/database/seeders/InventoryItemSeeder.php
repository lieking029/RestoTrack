<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::query()->each(function (Product $product) {
            InventoryItem::firstOrCreate(
                ['product_id' => $product->id],
                ['stock_quantity' => (int) $product->initial_stock, 'reorder_level' => 0]
            );
        });
    }
}
