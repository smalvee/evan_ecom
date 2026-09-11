<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('url_01')->nullable();
            $table->string('url_02')->nullable();
            $table->string('url_03')->nullable();
            $table->string('url_04')->nullable();
            $table->string('url_05')->nullable();
            $table->string('url_06')->nullable();
            $table->string('url_07')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn([
                'url_01', 'url_02', 'url_03', 'url_04', 'url_05', 'url_06', 'url_07',
            ]);
        });
    }
};
