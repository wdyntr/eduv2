<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin12345'),
                'nama' => 'Administrator Sistem',
                'sekolah_id' => null,
            ]
        );
        $admin->assignRole('admin_sistem');

        $penulis = User::updateOrCreate(
            ['username' => 'penulis'],
            [
                'password' => Hash::make('penulis123'),
                'nama' => 'Penulis Jurnal',
                'sekolah_id' => null,
            ]
        );
        $penulis->assignRole('penulis');

        $review = User::updateOrCreate(
            ['username' => 'review'],
            [
                'password' => Hash::make('review123'),
                'nama' => 'Review Jurnal',
                'sekolah_id' => null,
            ]
        );
        // Catatan: nama role di sini harus SAMA dengan yang dibuat RolePermissionSeeder ('pereview').
        // Kalau kamu mau nama tampilannya "Review Jurnal", ubah di RolePermissionSeeder juga,
        // jangan cuma rename manual lewat halaman Kelola Role.
        $review->assignRole('reviewer_jurnal');

        $operator = User::updateOrCreate(
            ['username' => 'operator'],
            [
                'password' => Hash::make('operator123'),
                'nama' => 'Operator Konten',
                'sekolah_id' => null,
            ]
        );
        $operator->assignRole('operator_konten');
    }
}