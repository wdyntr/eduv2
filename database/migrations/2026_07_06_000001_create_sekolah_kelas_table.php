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
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uniq_sekolah_mapel` (`sekolah_id`,`mapel_id`),
                    KEY `idx_sekolah_kelas_sekolah` (`sekolah_id`),
                    KEY `idx_sekolah_kelas_mapel` (`mapel_id`),
                    CONSTRAINT `fk_sekolah_kelas_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_sekolah_kelas_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah_kelas');
    }
};