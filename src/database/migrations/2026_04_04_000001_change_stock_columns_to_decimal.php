<?php

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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('initial_stock', 10, 2)->default(0)->change();
            $table->decimal('stock_out', 10, 2)->nullable()->change();
            $table->decimal('remaining_stock', 10, 2)->nullable()->change();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('stock_quantity', 10, 2)->default(0)->change();
            $table->decimal('reorder_level', 10, 2)->default(0)->change();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('initial_stock')->default(0)->change();
            $table->integer('stock_out')->nullable()->change();
            $table->integer('remaining_stock')->nullable()->change();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->change();
            $table->integer('reorder_level')->default(0)->change();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
