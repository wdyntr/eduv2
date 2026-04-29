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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();             // soal bisa tanpa teks bacaan
            $table->string('paket');                      // ✅ pindah ke sini
            $table->longText('passage_highlighted')->nullable(); // versi teks + <em>
            $table->text('question_text')->nullable();
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('option_e')->nullable();
            $table->enum('correct_answer', ['A','B','C','D', 'E']);
            $table->integer('points')->default(1);
            $table->string('subject_matter')->nullable(); // kolom "Materi" di Word
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
