<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materi')) {
            DB::statement("
                CREATE TABLE `materi` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `judul` varchar(255) NOT NULL,
                    `deskripsi` text,
                    `tipe` enum('video','ppt','pdf') NOT NULL,
                    `jenjang` enum('sma','smk','slb') NOT NULL,
                    `mapel_id` int NOT NULL,
                    `url` varchar(500) NOT NULL,
                    `thumbnail` varchar(500) DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    `is_active` tinyint(1) DEFAULT 1,
                    PRIMARY KEY (`id`),
                    KEY `idx_materi_jenjang` (`jenjang`),
                    KEY `idx_materi_tipe` (`tipe`),
                    KEY `idx_materi_mapel` (`mapel_id`),
                    KEY `idx_materi_active` (`is_active`),
                    CONSTRAINT `materi_ibfk_1` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};