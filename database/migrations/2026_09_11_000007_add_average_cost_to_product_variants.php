<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds the current weighted-average inventory cost. Existing variants are
     * backfilled from their current purchase_price (the existing cost basis).
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('average_cost', 12, 2)->nullable()->after('purchase_price');
        });

        DB::statement('
            UPDATE product_variants
            SET average_cost = CAST(purchase_price AS DECIMAL(12,2))
            WHERE average_cost IS NULL
              AND purchase_price IS NOT NULL
              AND purchase_price <> ""
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('average_cost');
        });
    }
};
