<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50)->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->double('subtotal', 10,2);
            $table->double('shipping', 10,2);
            $table->string('coupon_code')->nullable();
            $table->double('discount', 10,2);
            $table->integer('additional_discount')->default(0);
            $table->double('grand_total', 10,2);


            // User Address related columns

            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->text('notes')->nullable();
            $table->string('status', 11)->default('pending');
            $table->string('admin_note', 500)->nullable();
            $table->boolean('payment_status')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
