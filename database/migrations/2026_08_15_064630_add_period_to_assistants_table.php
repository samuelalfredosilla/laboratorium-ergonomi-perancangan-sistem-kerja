<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assistants', function (Blueprint $table) {
            $table->string('period', 20)->default('2025/2026')->after('division'); // Contoh: 2025/2026
            $table->boolean('is_active_period')->default(true)->after('period'); // Periode aktif yang tampil default
        });
    }

    public function down(): void
    {
        Schema::table('assistants', function (Blueprint $table) {
            $table->dropColumn(['period', 'is_active_period']);
        });
    }
};
