<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Pre-order support.
     *
     * - product_variants.allow_pre_order: admin opt-in, per variant (stock lives here).
     * - order_items.is_pre_order: historical flag; stays true even if the admin later
     *   disables pre-order for the product.
     * - order_items.pre_order_status: pre-order workflow state (pending/processing/
     *   completed/cancelled). "Stock Available" is derived from current stock.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('product_variants', 'allow_pre_order')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->boolean('allow_pre_order')->default(false)->after('qty');
            });
        }

        if (!Schema::hasColumn('order_items', 'is_pre_order')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->boolean('is_pre_order')->default(false)->after('free_delivery');
            });
        }

        if (!Schema::hasColumn('order_items', 'pre_order_status')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('pre_order_status', 20)->nullable()->after('is_pre_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_variants', 'allow_pre_order')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('allow_pre_order');
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['is_pre_order', 'pre_order_status']);
        });
    }
};
