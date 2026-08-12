<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_sessions')) {
            DB::statement("
                CREATE TABLE `admin_sessions` (
                    `token` varchar(64) NOT NULL,
                    `admin_id` int NOT NULL,
                    `username` varchar(100) NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `expires_at` datetime NOT NULL,
                    PRIMARY KEY (`token`),
                    KEY `admin_id` (`admin_id`),
                    CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sessions');
    }
};