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
                    `nama` varchar(100) NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `jurnal_kategori_nama_unique` (`nama`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
            ");

            DB::table('jurnal_kategori')->insert([
                ['nama' => 'Pendidikan', 'created_at' => now()],
                ['nama' => 'Sains & Teknologi', 'created_at' => now()],
                ['nama' => 'Sosial Humaniora', 'created_at' => now()],
                ['nama' => 'Bahasa & Sastra', 'created_at' => now()],
                ['nama' => 'Ekonomi & Bisnis', 'created_at' => now()],
                ['nama' => 'Lainnya', 'created_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_kategori');
    }
};
