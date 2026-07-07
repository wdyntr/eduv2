<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('admin', 'role')) {
            DB::statement("ALTER TABLE admin ADD COLUMN role ENUM('admin','penulis') NOT NULL DEFAULT 'admin' AFTER nama");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admin', 'role')) {
            DB::statement("ALTER TABLE admin DROP COLUMN role");
        }
    }
};
