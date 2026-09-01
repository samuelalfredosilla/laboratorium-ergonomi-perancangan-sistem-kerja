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
        Schema::create('laboratory_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');

            // Kolom jadwal dan lokasi acara
            $table->date('event_date');
            $table->string('event_time'); // Menggunakan string agar bisa menampung format seperti "08:00 - Selesai"
            $table->string('event_place');

            // Link Google Drive
            $table->string('gdrive_link')->nullable();

            // Tanggal publikasi khusus
            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_events');
    }
};
