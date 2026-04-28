<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──
        DB::table('users')->insert([
            'name'       => 'Administrator',
            'username'   => 'admin',
            'password'   => Hash::make('admin123'),
            'role'       => 'admin',
            'kelas'      => null,
            'no_induk'   => null,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Contoh Siswa ──
        $siswa = [
            [
                'name'      => 'Budi Santoso',
                'username'  => 'budi.santoso',
                'password'  => Hash::make('siswa123'),
                'kelas'     => 'XII IPA 1',
                'no_induk'  => '1234567890',
            ],
            [
                'name'      => 'Siti Rahayu',
                'username'  => 'siti.rahayu',
                'password'  => Hash::make('siswa123'),
                'kelas'     => 'XII IPA 1',
                'no_induk'  => '1234567891',
            ],
            [
                'name'      => 'Ahmad Fauzi',
                'username'  => 'ahmad.fauzi',
                'password'  => Hash::make('siswa123'),
                'kelas'     => 'XII IPA 2',
                'no_induk'  => '1234567892',
            ],
        ];

        foreach ($siswa as $s) {
            DB::table('users')->insert([
                'name'       => $s['name'],
                'username'   => $s['username'],
                'password'   => $s['password'],
                'role'       => 'siswa',
                'kelas'      => $s['kelas'],
                'no_induk'   => $s['no_induk'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
