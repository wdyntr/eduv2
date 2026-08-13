<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jurnal_review')) {
            DB::statement("
                CREATE TABLE `jurnal_review` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `jurnal_revisi_id` int NOT NULL,
                    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                    `catatan_admin` text,
                    `reviewed_by` int DEFAULT NULL COMMENT 'user yang approve/reject',
                    `reviewed_at` datetime DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_jurnal_review_revisi` (`jurnal_revisi_id`),
                    KEY `idx_jurnal_review_status` (`status`),
                    KEY `idx_jurnal_review_reviewer` (`reviewed_by`),
                    CONSTRAINT `fk_jurnal_review_revisi` FOREIGN KEY (`jurnal_revisi_id`) REFERENCES `jurnal_revisi` (`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_jurnal_review_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_review');
    }
};