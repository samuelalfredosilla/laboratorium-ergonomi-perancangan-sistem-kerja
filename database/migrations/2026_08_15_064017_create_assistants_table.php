<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nim', 30)->nullable();
            $table->string('division')->nullable(); // Koordinator, Praktikum, Riset, Inventaris, dll
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistants');
    }
};
