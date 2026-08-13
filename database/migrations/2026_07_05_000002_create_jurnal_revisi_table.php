<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jurnal_revisi')) {
            DB::statement("
                CREATE TABLE `jurnal_revisi` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `jurnal_id` int NOT NULL,
                    `versi_ke` int NOT NULL DEFAULT 1,
                    `judul` varchar(255) NOT NULL,
                    `penulis` varchar(255) NOT NULL COMMENT 'nama penulis tercantum di jurnal, bisa lebih dari satu',
                    `abstrak` text,
                    `jumlah_halaman` int NOT NULL DEFAULT 0,
                    `tahun_terbit` int NOT NULL DEFAULT " . date('Y') . ",
                    `volume` varchar(50) DEFAULT NULL,
                    `nomor_edisi` varchar(50) DEFAULT NULL,
                    `issn` varchar(50) DEFAULT NULL,
                    `kata_kunci` varchar(255) DEFAULT NULL,
                    `bahasa` varchar(30) NOT NULL DEFAULT 'Indonesia',
                    `file_jurnal` varchar(500) NOT NULL,
                    `file_bukti_plagiarisme` varchar(500) NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uniq_jurnal_versi` (`jurnal_id`,`versi_ke`),
                    KEY `idx_jurnal_revisi_jurnal` (`jurnal_id`),
                    CONSTRAINT `fk_jurnal_revisi_jurnal` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_revisi');
    }
};