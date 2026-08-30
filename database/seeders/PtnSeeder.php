<?php

namespace Database\Seeders;

use App\Models\Ptn;
use App\Models\PtnProdi;
use Illuminate\Database\Seeder;

class PtnSeeder extends Seeder
{
    public function run(): void
    {
        $ptns = [
            [
                'ptn' => 'Universitas Syiah Kuala',
                'logo' => 'https://eduzone.id/wp-content/uploads/2025/01/logo-universitas-syiah-kuala-USK-150x150.png',
                'prodi' => [
                    ['kode' => '111001', 'prodi' => 'Pendidikan Dokter Hewan - USK', 'jenjang' => 'S1', 'grade' => 420.98, 'persen' => 0.421],
                    ['kode' => '111002', 'prodi' => 'Teknik Sipil - USK', 'jenjang' => 'S1', 'grade' => 480.60, 'persen' => 0.481],
                    ['kode' => '111003', 'prodi' => 'Teknik Mesin - USK', 'jenjang' => 'S1', 'grade' => 484.20, 'persen' => 0.484],
                    ['kode' => '111004', 'prodi' => 'Teknik Kimia - USK', 'jenjang' => 'S1', 'grade' => 477.00, 'persen' => 0.477],
                    ['kode' => '111005', 'prodi' => 'Arsitektur - USK', 'jenjang' => 'S1', 'grade' => 466.54, 'persen' => 0.467],
                ],
            ],
            [
                'ptn' => 'Universitas Malikussaleh',
                'logo' => 'https://eduzone.id/wp-content/uploads/2025/01/logo-unimal-no-background-237x300.png',
                'prodi' => [
                    ['kode' => '112001', 'prodi' => 'Teknik Informatika - Unimal', 'jenjang' => 'S1', 'grade' => 430.35, 'persen' => 0.430],
                    ['kode' => '112002', 'prodi' => 'Akuntansi - Unimal', 'jenjang' => 'S1', 'grade' => 405.80, 'persen' => 0.406],
                    ['kode' => '112003', 'prodi' => 'Administrasi Publik - Unimal', 'jenjang' => 'S1', 'grade' => 392.10, 'persen' => 0.392],
                ],
            ],
            [
                'ptn' => 'Universitas Sumatera Utara',
                'logo' => 'https://eduzone.id/wp-content/uploads/2025/01/Universitas-Sumatera-Utara-USU-no-background-150x150.png',
                'prodi' => [
                    ['kode' => '113001', 'prodi' => 'Kedokteran - USU', 'jenjang' => 'S1', 'grade' => 448.90, 'persen' => 0.449],
                    ['kode' => '113002', 'prodi' => 'Teknik Elektro - USU', 'jenjang' => 'S1', 'grade' => 432.45, 'persen' => 0.432],
                    ['kode' => '113003', 'prodi' => 'Psikologi - USU', 'jenjang' => 'S1', 'grade' => 418.35, 'persen' => 0.418],
                    ['kode' => '113004', 'prodi' => 'Manajemen - USU', 'jenjang' => 'S1', 'grade' => 410.20, 'persen' => 0.410],
                ],
            ],
        ];

        foreach ($ptns as $ptnData) {
            $ptn = Ptn::firstOrCreate([
                'ptn' => $ptnData['ptn'],
            ], [
                'logo' => $ptnData['logo'],
            ]);

            foreach ($ptnData['prodi'] as $prodiData) {
                PtnProdi::updateOrCreate(
                    ['kode' => $prodiData['kode']],
                    [
                        'ptn_id' => $ptn->id,
                        'prodi' => $prodiData['prodi'],
                        'jenjang' => $prodiData['jenjang'],
                        'grade' => $prodiData['grade'],
                        'persen' => $prodiData['persen'],
                    ]
                );
            }
        }
    }
}
