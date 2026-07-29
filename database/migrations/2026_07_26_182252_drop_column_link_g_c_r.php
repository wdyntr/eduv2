<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sekolah', 'classroom_url')) {
            DB::statement("ALTER TABLE sekolah DROP COLUMN classroom_url");
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('sekolah', 'classroom_url')) {
            DB::statement("ALTER TABLE sekolah ADD COLUMN classroom_url VARCHAR(500) NULL AFTER kota_kabupaten");
        }
    }
};