<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed the application's current contact information as editable defaults.
        $now = now();
        DB::table('settings')->insert([
            ['key' => 'site_phone', 'value' => '+880 1324 670 090', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_email', 'value' => 'Support@evan.com.bd', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_address', 'value' => 'Uttora, Sector 14', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
