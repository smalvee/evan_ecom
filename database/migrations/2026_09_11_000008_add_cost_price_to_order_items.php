<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds an inventory-cost snapshot per order item so historical profit does
     * not change when current costs/prices change later.
     *
     * Existing order items are backfilled from the variant's current cost so
     * existing reports keep producing the same numbers (best available data).
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->nullable()->after('price');
        });

        DB::statement('
            UPDATE order_items oi
            JOIN product_variants pv ON pv.id = oi.product_id
            SET oi.cost_price = CAST(COALESCE(pv.average_cost, pv.purchase_price) AS DECIMAL(12,2))
            WHERE oi.cost_price IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
