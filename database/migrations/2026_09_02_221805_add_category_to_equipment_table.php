<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Tambahkan kolom specification jika ternyata belum ada di database
            if (!Schema::hasColumn('equipment', 'specification')) {
                $table->string('specification')->nullable()->after('name');
            }
            
            // Tambahkan kolom category
            if (!Schema::hasColumn('equipment', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            if (Schema::hasColumn('equipment', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('equipment', 'specification')) {
                $table->dropColumn('specification');
            }
        });
    }
};