<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul Prosedur
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->string('file_url'); // Link Google Drive / PDF
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_procedures');
    }
};
