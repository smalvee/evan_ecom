<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shipping_charges', function (Blueprint $table) {
            $table->string('district')->nullable()->unique()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_charges', function (Blueprint $table) {
            $table->dropUnique(['district']);
            $table->dropColumn('district');
        });
    }
};
