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
        // database/migrations/xxxx_create_quiz_sessions_table.php
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('paket');
            $table->enum('subject', ['matematika', 'bahasa_inggris', 'bahasa_indonesia']);
            $table->string('kelas')->nullable(); // null = semua kelas
            $table->integer('durasi')->default(90);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('siswa_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('quiz_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('question_id')->constrained('questions');
            $table->enum('answer', ['A', 'B', 'C', 'D', 'E']);
            $table->boolean('is_correct')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id', 'question_id']);
        });

        Schema::create('quiz_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('quiz_sessions');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('score')->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('correct_count')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
        Schema::dropIfExists('siswa_answers');
        Schema::dropIfExists('quiz_hasil');
    }
};
