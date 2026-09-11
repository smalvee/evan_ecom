<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('stock_deducted')->default(false)->after('payment_status');
        });

        // Backfill: previously stock was reduced at order placement for every order,
        // so mark all existing orders as already deducted to avoid double deduction.
        DB::table('orders')->update(['stock_deducted' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_deducted');
        });
    }
};
