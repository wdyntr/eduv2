
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom role ke tabel admin terlebih dahulu
        if (!Schema::hasColumn('admin', 'role')) {
            DB::statement("ALTER TABLE admin ADD COLUMN role ENUM('admin','penulis') NOT NULL DEFAULT 'admin' AFTER nama");
        }

        // 2. Buat tabel jurnal setelah kolom admin siap
        if (!Schema::hasTable('jurnal')) {
            DB::statement("
                CREATE TABLE `jurnal` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `judul` varchar(255) NOT NULL,
                    `kategori` varchar(100) NOT NULL,
                    `penulis` varchar(255) NOT NULL,
                    `abstrak` text,
                    `file_jurnal` varchar(500) NOT NULL,
                    `file_bukti_plagiarisme` varchar(500) NOT NULL,
                    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                    `catatan_admin` text,
                    `admin_id` int NOT NULL COMMENT 'akun penulis yang mengajukan',
                    `reviewed_by` int DEFAULT NULL COMMENT 'admin yang approve/reject',
                    `reviewed_at` datetime DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_jurnal_status` (`status`),
                    KEY `idx_jurnal_kategori` (`kategori`),
                    KEY `idx_jurnal_admin` (`admin_id`),
                    CONSTRAINT `jurnal_admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE,
                    CONSTRAINT `jurnal_reviewer_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `admin` (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
            ");
        }
    }

    public function down(): void
    {
        // 1. Hapus tabel jurnal terlebih dahulu untuk memutus foreign key
        Schema::dropIfExists('jurnal');

        // 2. Hapus kolom role dari tabel admin setelahnya
        if (Schema::hasColumn('admin', 'role')) {
            DB::statement("ALTER TABLE admin DROP COLUMN role");
        }
    }
};

