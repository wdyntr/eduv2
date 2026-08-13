<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jurnal')) {
            DB::statement("
                CREATE TABLE `jurnal` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `kategori_id` int NOT NULL,
                    `user_id` int NOT NULL COMMENT 'akun penulis yang mengajukan',
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_jurnal_kategori` (`kategori_id`),
                    KEY `idx_jurnal_user` (`user_id`),
                    CONSTRAINT `fk_jurnal_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `jurnal_kategori` (`id`),
                    CONSTRAINT `fk_jurnal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};