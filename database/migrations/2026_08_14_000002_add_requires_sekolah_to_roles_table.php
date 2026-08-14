<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('roles', 'requires_sekolah')) {
            DB::statement("
                ALTER TABLE `roles`
                ADD COLUMN `requires_sekolah` TINYINT(1) NOT NULL DEFAULT 0
            ");
        }

        // Migrasi data: role yang sudah ada namanya 'sekolah' otomatis ditandai.
        DB::table('roles')->where('name', 'sekolah')->update(['requires_sekolah' => 1]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('roles', 'requires_sekolah')) {
            DB::statement("ALTER TABLE `roles` DROP COLUMN `requires_sekolah`");
        }
    }
};