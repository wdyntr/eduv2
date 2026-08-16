<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('artikel_kategori')) {
            DB::statement("
                CREATE TABLE `artikel_kategori` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `nama` varchar(100) NOT NULL,
                    `slug` varchar(120) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `artikel_kategori_slug_unique` (`slug`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!Schema::hasColumn('artikel', 'kategori_id')) {
            DB::statement("ALTER TABLE `artikel` ADD COLUMN `kategori_id` int DEFAULT NULL AFTER `slug`");
            DB::statement("ALTER TABLE `artikel` ADD CONSTRAINT `fk_artikel_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `artikel_kategori` (`id`)");
        }
        if (!Schema::hasColumn('artikel', 'tipe')) {
            DB::statement("ALTER TABLE `artikel` ADD COLUMN `tipe` ENUM('artikel','video') NOT NULL DEFAULT 'artikel' AFTER `kategori_id`");
        }
        if (!Schema::hasColumn('artikel', 'video_url')) {
            DB::statement("ALTER TABLE `artikel` ADD COLUMN `video_url` varchar(500) DEFAULT NULL AFTER `tipe`");
        }
    }

    public function down(): void
    {
        Schema::table('artikel', function ($table) {
            if (Schema::hasColumn('artikel', 'kategori_id')) $table->dropForeign('fk_artikel_kategori');
        });
        DB::statement("ALTER TABLE `artikel` DROP COLUMN IF EXISTS `kategori_id`, DROP COLUMN IF EXISTS `tipe`, DROP COLUMN IF EXISTS `video_url`");
        Schema::dropIfExists('artikel_kategori');
    }
};