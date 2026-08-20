<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authentication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('username')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['SUCCESS', 'FAILED', 'LOCKED_OUT']);
            $table->string('message')->nullable();
            $table->timestamp('login_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authentication_logs');
    }
};
