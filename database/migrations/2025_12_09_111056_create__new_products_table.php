<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('new_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('sku')->nullable();
            $table->string('unit_id')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('cat_id')->nullable();
            $table->string('sub_cat_id')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('hot_products')->default(false);
            $table->timestamps();
        });

        // product_variants is created before this table, so its foreign key is
        // added here (after new_products exists) instead of in its own migration.
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->foreign('product_id')->references('id')->on('new_products')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_products');
    }
};
