<?php

use App\Enums\OrderStatus;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
        $table->uuid('id')->primary();

        $table->uuid('created_by');
        $table->uuid('processed_by')->nullable();

        $table->unsignedTinyInteger('status')->default(0);

        $table->decimal('subtotal', 10, 2)->default(0);
        $table->decimal('tax', 10, 2)->default(0);
        $table->decimal('total', 10, 2)->default(0);

        $table->timestamps();

        $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();

        $table->index('status');
        $table->index('created_at');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
