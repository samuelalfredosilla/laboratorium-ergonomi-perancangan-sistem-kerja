<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('practicum_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('gdrive_link');
            $table->string('collection_date'); // Contoh: Sabtu, 13 Mei 2026
            $table->string('collection_time'); // Contoh: 07.00 - 07.15 WIB
            $table->string('collection_place')->default('Ruang Laboratorium EPSK');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practicum_tasks');
    }
};
