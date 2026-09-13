<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('steadfast');
            // test | live
            $table->string('mode')->default('test');
            // Encrypted at rest via the model's `encrypted` cast.
            $table->text('api_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_settings');
    }
};
