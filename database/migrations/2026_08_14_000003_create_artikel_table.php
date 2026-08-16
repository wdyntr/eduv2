<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('artikel')) {
            DB::statement("
                CREATE TABLE `artikel` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `judul` varchar(255) NOT NULL,
                    `slug` varchar(255) NOT NULL,
                    `konten` longtext NOT NULL,
                    `thumbnail` varchar(500) DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    `is_active` tinyint(1) DEFAULT 1,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `artikel_slug_unique` (`slug`),
                    KEY `idx_artikel_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};