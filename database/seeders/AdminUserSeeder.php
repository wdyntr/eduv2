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
    }
}