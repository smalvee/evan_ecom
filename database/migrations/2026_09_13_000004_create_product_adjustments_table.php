<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_adjustments', function (Blueprint $table) {
            $table->id();
            // Filled in right after insert (ADJ-000001) so it is race-free.
            $table->string('adjustment_no')->nullable()->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            // increase | decrease | correction
            $table->string('adjustment_type');
            // Signed adjustment: +5, -5, +3, -8 ...
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            // Only set for Stock Correction (the entered physical stock).
            $table->integer('actual_stock')->nullable();
            $table->string('reason')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // No foreign keys: adjustments are historical and must survive
            // product/variant changes without blocking existing delete flows.
            $table->index('product_id');
            $table->index('variant_id');
            $table->index('adjustment_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_adjustments');
    }
};
