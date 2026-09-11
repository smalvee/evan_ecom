<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('new_products', function (Blueprint $table) {
            $table->boolean('free_delivery')->default(0);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('free_delivery')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('new_products', function (Blueprint $table) {
            $table->dropColumn('free_delivery');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('free_delivery');
        });
    }
};
