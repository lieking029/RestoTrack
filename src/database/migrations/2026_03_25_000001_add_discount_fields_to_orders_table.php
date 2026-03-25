<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('total'); // PWD, SENIOR
            $table->string('customer_name')->nullable()->after('discount_type');
            $table->string('id_number')->nullable()->after('customer_name');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('id_number');
            $table->decimal('discount_total', 10, 2)->default(0)->after('discount_amount'); // final total after discount
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'customer_name', 'id_number', 'discount_amount', 'discount_total']);
        });
    }
};
