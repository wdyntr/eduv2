<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jurnal_kategori')) {
            DB::statement("
                CREATE TABLE `jurnal_kategori` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `nama_kategori` varchar(100) NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `jurnal_kategori_nama_kategori_unique` (`nama_kategori`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::table('jurnal_kategori')->insert([
                ['nama_kategori' => 'Pendidikan', 'created_at' => now()],
                ['nama_kategori' => 'Sains & Teknologi', 'created_at' => now()],
                ['nama_kategori' => 'Sosial Humaniora', 'created_at' => now()],
                ['nama_kategori' => 'Bahasa & Sastra', 'created_at' => now()],
                ['nama_kategori' => 'Ekonomi & Bisnis', 'created_at' => now()],
                ['nama_kategori' => 'Lainnya', 'created_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_kategori');
    }
};