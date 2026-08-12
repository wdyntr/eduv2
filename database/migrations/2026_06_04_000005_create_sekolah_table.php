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
                    `jenjang` enum('sma','smk','slb') NOT NULL,
                    `kota_kabupaten` varchar(100) DEFAULT NULL,
                    `is_active` tinyint(1) DEFAULT 1,
                    PRIMARY KEY (`id`),
                    KEY `idx_sekolah_jenjang` (`jenjang`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};