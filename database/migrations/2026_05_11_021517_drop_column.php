<?php
// database/migrations/xxxx_drop_subject_from_quiz_sessions.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->enum('subject', ['matematika', 'bahasa_inggris', 'bahasa_indonesia'])
                  ->after('paket');
        });
    }
};
