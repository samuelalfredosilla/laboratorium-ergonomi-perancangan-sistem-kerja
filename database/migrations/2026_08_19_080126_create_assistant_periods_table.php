<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Contoh: 2025/2026, 2026/2027
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_periods');
    }
};