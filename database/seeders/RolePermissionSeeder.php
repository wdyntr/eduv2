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
            'jurnal.lihat',    // lihat daftar jurnal saja (admin_sistem)
            'jurnal.review',   // approve/reject/edit detail (reviewer_jurnal)
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
        $sekolah->requires_sekolah = true;
        $sekolah->save();

        $pereview = Role::firstOrCreate(['name' => 'reviewer_jurnal', 'guard_name' => 'web']);
        $pereview->syncPermissions(['jurnal.lihat', 'jurnal.review']);

        $adminSistem = Role::firstOrCreate(['name' => 'admin_sistem', 'guard_name' => 'web']);
        // admin_sistem dapat semua permission KECUALI jurnal.review —
        // sengaja dikecualikan supaya cuma bisa lihat jurnal, tidak bisa approve/reject/edit detail.
        $adminSistem->syncPermissions(
            Permission::whereNotIn('name', ['jurnal.review', 'jurnal.ajukan'])->pluck('name')->all()
        );
    }
}