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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->date('date');
            $table->unsignedBigInteger('variant_id');
            $table->string('p_name');
            $table->integer('qty');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0);
            $table->decimal('selling_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
