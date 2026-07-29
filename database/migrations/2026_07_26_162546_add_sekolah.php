<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan 'sekolah' ke pilihan role yang ada
        DB::statement("ALTER TABLE admin MODIFY COLUMN role ENUM('admin','guru','sekolah') NOT NULL DEFAULT 'admin'");

        // Kolom penghubung akun ber-role 'sekolah' ke satu baris data sekolah tertentu
        if (!Schema::hasColumn('admin', 'sekolah_id')) {
            DB::statement("ALTER TABLE admin ADD COLUMN sekolah_id INT NULL AFTER role");
            DB::statement("ALTER TABLE admin ADD CONSTRAINT fk_admin_sekolah FOREIGN KEY (sekolah_id) REFERENCES sekolah(id) ON DELETE SET NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admin', 'sekolah_id')) {
            DB::statement("ALTER TABLE admin DROP FOREIGN KEY fk_admin_sekolah");
            DB::statement("ALTER TABLE admin DROP COLUMN sekolah_id");
        }
        DB::statement("UPDATE admin SET role = 'admin' WHERE role = 'sekolah'");
        DB::statement("ALTER TABLE admin MODIFY COLUMN role ENUM('admin','guru') NOT NULL DEFAULT 'admin'");
    }
};