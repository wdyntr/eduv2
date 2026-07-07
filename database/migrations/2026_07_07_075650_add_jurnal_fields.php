<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('jurnal', 'jumlah_halaman')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN jumlah_halaman INT NOT NULL DEFAULT 0 AFTER abstrak");
        }
        if (!Schema::hasColumn('jurnal', 'tahun_terbit')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN tahun_terbit INT NOT NULL DEFAULT " . date('Y') . " AFTER jumlah_halaman");
        }
        if (!Schema::hasColumn('jurnal', 'volume')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN volume VARCHAR(50) NULL AFTER tahun_terbit");
        }
        if (!Schema::hasColumn('jurnal', 'nomor_edisi')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN nomor_edisi VARCHAR(50) NULL AFTER volume");
        }
        if (!Schema::hasColumn('jurnal', 'issn')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN issn VARCHAR(50) NULL AFTER nomor_edisi");
        }
        if (!Schema::hasColumn('jurnal', 'kata_kunci')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN kata_kunci VARCHAR(255) NULL AFTER issn");
        }
        if (!Schema::hasColumn('jurnal', 'bahasa')) {
            DB::statement("ALTER TABLE jurnal ADD COLUMN bahasa VARCHAR(30) NOT NULL DEFAULT 'Indonesia' AFTER kata_kunci");
        }
    }

    public function down(): void
    {
        foreach (['jumlah_halaman', 'tahun_terbit', 'volume', 'nomor_edisi', 'issn', 'kata_kunci', 'bahasa'] as $col) {
            if (Schema::hasColumn('jurnal', $col)) {
                DB::statement("ALTER TABLE jurnal DROP COLUMN {$col}");
            }
        }
    }
};
