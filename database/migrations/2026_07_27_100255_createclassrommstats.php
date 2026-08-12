<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('classroom_kelas_stats')) {
            DB::statement("
                CREATE TABLE `classroom_kelas_stats` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `sekolah_kelas_id` int NOT NULL,
                    `bulan` char(7) NOT NULL COMMENT 'format YYYY-MM',
                    `jumlah_guru` int NOT NULL DEFAULT 0,
                    `jumlah_siswa` int NOT NULL DEFAULT 0,
                    `jumlah_task` int NOT NULL DEFAULT 0 COMMENT 'courseWork yang dibuat guru bulan ini',
                    `jumlah_materi` int NOT NULL DEFAULT 0 COMMENT 'courseWorkMaterials (upload) bulan ini',
                    `synced_at` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uniq_kelas_bulan` (`sekolah_kelas_id`,`bulan`),
                    CONSTRAINT `classroom_kelas_stats_ibfk_1` FOREIGN KEY (`sekolah_kelas_id`) REFERENCES `sekolah_kelas` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_kelas_stats');
    }
};