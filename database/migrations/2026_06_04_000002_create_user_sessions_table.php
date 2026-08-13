<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_sessions')) {
            DB::statement("
                CREATE TABLE `user_sessions` (
                    `token` varchar(64) NOT NULL,
                    `user_id` int NOT NULL,
                    `username` varchar(100) NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `expires_at` datetime NOT NULL,
                    PRIMARY KEY (`token`),
                    KEY `idx_user_sessions_user` (`user_id`),
                    CONSTRAINT `fk_user_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};