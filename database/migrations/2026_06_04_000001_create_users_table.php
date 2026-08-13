<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            DB::statement("
                CREATE TABLE `users` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `username` varchar(100) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `nama` varchar(150) DEFAULT NULL,
                    `sekolah_id` int DEFAULT NULL COMMENT 'diisi kalau akun operator konten dikaitkan ke 1 sekolah tertentu',
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `users_username_unique` (`username`),
                    KEY `idx_users_sekolah` (`sekolah_id`),
                    CONSTRAINT `fk_users_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};