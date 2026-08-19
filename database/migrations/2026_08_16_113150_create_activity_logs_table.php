<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Admin yang melakukan aksi
            $table->string('subject_type'); // Model terkait (misal: App\Models\Assistant)
            $table->unsignedBigInteger('subject_id')->nullable(); // ID record terkait
            $table->string('action'); // created, updated, deleted
            $table->string('description'); // Penjelasan singkat
            $table->json('properties')->nullable(); // Menyimpan data sebelum & sesudah (old & new values)
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
