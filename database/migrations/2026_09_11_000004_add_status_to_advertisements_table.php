<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds a per-slot active status flag to the advertisements row.
     * Defaults to active (1) so existing advertisements keep showing.
     */
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            for ($i = 1; $i <= 7; $i++) {
                $table->boolean('status_0' . $i)->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $columns = [];
            for ($i = 1; $i <= 7; $i++) {
                $columns[] = 'status_0' . $i;
            }
            $table->dropColumn($columns);
        });
    }
};
