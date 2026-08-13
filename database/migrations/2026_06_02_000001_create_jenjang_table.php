<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jenjang')) {
            DB::statement("
                CREATE TABLE `jenjang` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `kode` varchar(20) NOT NULL,
                    `nama` varchar(100) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `jenjang_kode_unique` (`kode`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::table('jenjang')->insert([
                ['kode' => 'sma', 'nama' => 'Sekolah Menengah Atas'],
                ['kode' => 'smk', 'nama' => 'Sekolah Menengah Kejuruan'],
                ['kode' => 'slb', 'nama' => 'Sekolah Luar Biasa'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenjang');
    }
};