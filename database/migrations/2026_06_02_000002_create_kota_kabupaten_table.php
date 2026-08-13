<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kota_kabupaten')) {
            DB::statement("
                CREATE TABLE `kota_kabupaten` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `nama` varchar(100) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `kota_kabupaten_nama_unique` (`nama`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::table('kota_kabupaten')->insert(collect([
                'Bandar Lampung', 'Metro', 'Lampung Selatan', 'Lampung Tengah', 'Lampung Utara',
                'Lampung Barat', 'Lampung Timur', 'Tanggamus', 'Tulang Bawang', 'Tulang Bawang Barat',
                'Way Kanan', 'Pesawaran', 'Pringsewu', 'Mesuji', 'Pesisir Barat',
            ])->map(fn($nama) => ['nama' => $nama])->all());
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kota_kabupaten');
    }
};