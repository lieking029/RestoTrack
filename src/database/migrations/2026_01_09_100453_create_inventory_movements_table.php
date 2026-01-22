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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('inventory_item_id');
            $table->uuid('order_id')->nullable();
            $table->uuid('performed_by')->nullable();

            $table->enum('type', ['DEBIT', 'CREDIT']);
            $table->enum('reason', ['SALE', 'CANCEL', 'ADJUSTMENT', 'WASTE', 'RECEIVING']);

            $table->integer('quantity');
            $table->string('note')->nullable();

            $table->timestamps();

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->cascadeOnDelete();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->nullOnDelete();

            $table->foreign('performed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['order_id', 'reason']);
            $table->index(['inventory_item_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
