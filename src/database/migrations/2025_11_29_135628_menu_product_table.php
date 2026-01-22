<?php

use App\Models\Menu;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_product', function (Blueprint $table) {
            $table->foreignIdFor(Menu::class);
            $table->foreignIdFor(Product::class);
            $table->decimal('quantity_needed', 10, 2)->default(1);
            $table->primary(['menu_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_product');
    }
};
