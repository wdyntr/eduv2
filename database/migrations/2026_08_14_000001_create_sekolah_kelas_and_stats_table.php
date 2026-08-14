<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sekolah_kelas')) {
            DB::statement("
                CREATE TABLE `sekolah_kelas` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `sekolah_id` int NOT NULL,
                    `mapel_id` int NOT NULL,
                    `classroom_url` varchar(500) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uq_sekolah_kelas` (`sekolah_id`,`mapel_id`),
                    KEY `idx_sekolah_kelas_sekolah` (`sekolah_id`),
                    KEY `idx_sekolah_kelas_mapel` (`mapel_id`),
                    CONSTRAINT `fk_sekolah_kelas_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_sekolah_kelas_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!Schema::hasTable('classroom_kelas_stats')) {
            DB::statement("
                CREATE TABLE `classroom_kelas_stats` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `sekolah_kelas_id` int NOT NULL,
                    `bulan` varchar(7) NOT NULL,
                    `jumlah_guru` int DEFAULT NULL,
                    `jumlah_siswa` int DEFAULT NULL,
                    `jumlah_task` int DEFAULT NULL,
                    `jumlah_materi` int DEFAULT NULL,
                    `synced_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uq_stats_kelas_bulan` (`sekolah_kelas_id`,`bulan`),
                    CONSTRAINT `fk_stats_sekolah_kelas` FOREIGN KEY (`sekolah_kelas_id`) REFERENCES `sekolah_kelas` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_kelas_stats');
        Schema::dropIfExists('sekolah_kelas');
    }
};