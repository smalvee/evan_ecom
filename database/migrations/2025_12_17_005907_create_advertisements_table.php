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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name_01');
            $table->string('image_01');

            $table->string('name_02');
            $table->string('image_02');

            $table->string('name_03');
            $table->string('image_03');

            $table->string('name_04');
            $table->string('image_04');

            $table->string('name_05');
            $table->string('image_05');

            $table->string('name_06');
            $table->string('image_06');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
