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
        Schema::create('siswa_quiz_starts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('quiz_sessions')->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('deadline_at'); // started_at + durasi
            $table->timestamps();

            $table->unique(['user_id', 'session_id']); // satu siswa satu record per sesi

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_quiz_starts');
    }
};
