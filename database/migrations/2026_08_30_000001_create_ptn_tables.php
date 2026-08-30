<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptn', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('ptn');
            $table->timestamps();
        });

        Schema::create('ptn_prodi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('ptn_id')->constrained('ptn')->cascadeOnDelete();
            $table->string('prodi');
            $table->string('jenjang')->default('S1');
            $table->decimal('grade', 8, 2)->default(0);
            $table->decimal('persen', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptn_prodi');
        Schema::dropIfExists('ptn');
    }
};
