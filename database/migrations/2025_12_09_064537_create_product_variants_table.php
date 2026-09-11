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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('new_products')->onDelete('cascade');
            $table->string('sku')->unique();
            $table->boolean('is_variant')->default(false);
            $table->string('purchase_price')->nullable();
            $table->string('selling_price')->nullable();
            $table->string('compare_price')->nullable();
            $table->string('qty')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
