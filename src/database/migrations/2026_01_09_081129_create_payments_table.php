<?php

use App\Models\Order;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'processed_by')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method'); // cash, gcash, card, etc.
            $table->timestamps();

            $table->index('order_id');
            $table->index('processed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
