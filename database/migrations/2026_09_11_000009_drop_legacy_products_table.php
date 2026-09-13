<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Drop the legacy `products` table (superseded by new_products / product_variants).
     *
     * Any foreign key that still references `products` is removed first so the
     * drop cannot fail on drifted schemas. `product_images` is shared with the
     * new product system and is intentionally kept.
     */
    public function up(): void
    {
        $constraints = DB::select("
            SELECT TABLE_NAME, CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND REFERENCED_TABLE_NAME = 'products'
              AND TABLE_NAME <> 'products'
        ");

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE `{$constraint->TABLE_NAME}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }

        Schema::dropIfExists('products');
    }

    /**
     * Reverse the migrations (structure only — legacy data is not restored).
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->double('price', 10, 2);
            $table->double('compare_price', 10, 2)->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('is_featured', ['Yes', 'No'])->default('No');
            $table->string('sku');
            $table->string('barcode')->nullable();
            $table->enum('track_qty', ['Yes', 'No'])->default('No');
            $table->integer('qty')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }
};
