<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // "pengunjung" publik tidak login,

        $permissions = [
            // konten materi
            'materi.kelola',
            // direktori classroom (sekolah, mata pelajaran, link kelas per mapel)
            'classroom.kelola',
            // review pengajuan jurnal
            'jurnal.review',
            // mengajukan jurnal sebagai penulis
            'jurnal.ajukan',
            // kelola akun & hak akses pengguna
            'users.kelola',
            // kelola pengaturan teknis sistem (kategori jurnal, mapel, jenjang, dst)
            'sistem.kelola',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $penulis = Role::firstOrCreate(['name' => 'penulis', 'guard_name' => 'web']);
        $penulis->syncPermissions(['jurnal.ajukan']);

        $operatorKonten = Role::firstOrCreate(['name' => 'operator_konten', 'guard_name' => 'web']);
        $operatorKonten->syncPermissions(['materi.kelola', 'classroom.kelola', 'jurnal.review']);

        $adminSistem = Role::firstOrCreate(['name' => 'admin_sistem', 'guard_name' => 'web']);
        
        // admin sistem otomatis dapat semua akses
        $adminSistem->syncPermissions(Permission::pluck('name')->all()); 
    }
}