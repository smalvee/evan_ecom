<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_shipment_id')->constrained('courier_shipments')->cascadeOnDelete();
            $table->string('status');
            $table->string('message')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('courier_shipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_status_histories');
    }
};
