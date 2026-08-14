<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // "Pengunjung" publik tidak login, jadi tidak butuh row akun/role.

        $permissions = [
            'materi.kelola',
            'classroom.kelola',
            'jurnal.review',
            'jurnal.ajukan',
            'users.kelola',
            'sistem.kelola',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $penulis = Role::firstOrCreate(['name' => 'penulis', 'guard_name' => 'web']);
        $penulis->syncPermissions(['jurnal.ajukan']);

        $operatorKonten = Role::firstOrCreate(['name' => 'operator_konten', 'guard_name' => 'web']);
        $operatorKonten->syncPermissions(['materi.kelola', 'classroom.kelola']);

        $sekolah = Role::firstOrCreate(['name' => 'sekolah', 'guard_name' => 'web']);
        $sekolah->syncPermissions(['classroom.kelola']);

        $pereview = Role::firstOrCreate(['name'=> 'pereview', 'guard_name' => 'web']);
        $pereview->syncPermissions(['jurnal.review',]);

        $adminSistem = Role::firstOrCreate(['name' => 'admin_sistem', 'guard_name' => 'web']);
        $adminSistem->syncPermissions(Permission::pluck('name')->all());
    }
}