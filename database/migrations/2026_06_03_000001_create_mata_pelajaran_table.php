<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mata_pelajaran')) {
            DB::statement("
                CREATE TABLE `mata_pelajaran` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `nama` varchar(100) NOT NULL,
                    `jenjang_id` int NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uq_mapel` (`nama`,`jenjang_id`),
                    KEY `idx_mapel_jenjang` (`jenjang_id`),
                    CONSTRAINT `fk_mapel_jenjang` FOREIGN KEY (`jenjang_id`) REFERENCES `jenjang` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};