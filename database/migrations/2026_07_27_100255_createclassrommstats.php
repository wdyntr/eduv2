<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('classroom_kelas_stats')) {
            DB::statement("
                CREATE TABLE classroom_kelas_stats (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sekolah_kelas_id INT NOT NULL,
                    bulan CHAR(7) NOT NULL COMMENT 'format YYYY-MM',
                    jumlah_guru INT NOT NULL DEFAULT 0,
                    jumlah_siswa INT NOT NULL DEFAULT 0,
                    jumlah_task INT NOT NULL DEFAULT 0 COMMENT 'courseWork yang dibuat guru bulan ini',
                    jumlah_materi INT NOT NULL DEFAULT 0 COMMENT 'courseWorkMaterials (upload) bulan ini',
                    synced_at DATETIME NOT NULL,
                    UNIQUE KEY uniq_kelas_bulan (sekolah_kelas_id, bulan),
                    FOREIGN KEY (sekolah_kelas_id) REFERENCES sekolah_kelas(id) ON DELETE CASCADE
                )
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_kelas_stats');
    }
};