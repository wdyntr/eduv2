<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sekolah')) {
            DB::statement("
                CREATE TABLE `sekolah` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `nama` varchar(255) NOT NULL,
                    `jenjang_id` int NOT NULL,
                    `kota_kabupaten_id` int DEFAULT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    PRIMARY KEY (`id`),
                    KEY `idx_sekolah_jenjang` (`jenjang_id`),
                    KEY `idx_sekolah_kota` (`kota_kabupaten_id`),
                    CONSTRAINT `fk_sekolah_jenjang` FOREIGN KEY (`jenjang_id`) REFERENCES `jenjang` (`id`),
                    CONSTRAINT `fk_sekolah_kota` FOREIGN KEY (`kota_kabupaten_id`) REFERENCES `kota_kabupaten` (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};