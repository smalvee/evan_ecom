<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider')->default('steadfast');
            $table->string('mode')->default('test');
            $table->string('invoice')->nullable();
            $table->string('consignment_id')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('cod_amount', 12, 2)->default(0);
            $table->string('delivery_type')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('recipient_address')->nullable();
            $table->text('item_description')->nullable();
            $table->unsignedInteger('total_lot')->nullable();
            $table->json('response_data')->nullable();

            // 1 = this shipment occupies the order's single active slot.
            // NULL = released (cancelled/failed), so a retry is allowed.
            // The unique index below is the DB-level duplicate protection:
            // MySQL allows many NULLs but only one row per (order_id, 1).
            $table->boolean('active')->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('consignment_id');
            $table->index('tracking_code');
            $table->index('invoice');
            $table->index('status');
            $table->unique(['order_id', 'active'], 'courier_shipments_order_active_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_shipments');
    }
};
