<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_settings', function (Blueprint $table) {
            // Optional custom API base URL (e.g. when the provider changes it).
            // When null, config('courier.providers.*.base_url') is used.
            $table->string('base_url')->nullable()->after('mode');
        });
    }

    public function down(): void
    {
        Schema::table('courier_settings', function (Blueprint $table) {
            $table->dropColumn('base_url');
        });
    }
};
