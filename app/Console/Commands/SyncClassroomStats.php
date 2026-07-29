<?php

namespace App\Console\Commands;

use App\Models\SekolahKelas;
use App\Services\GoogleClassroomService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncClassroomStats extends Command
{
    protected $signature = 'classroom:sync-stats';
    protected $description = 'Sinkronisasi jumlah guru, siswa, task, dan materi dari Google Classroom per mata pelajaran';

    public function handle(): int
    {
        if (!config('services.google_classroom.impersonate_email')) {
            $this->error('GOOGLE_CLASSROOM_ADMIN_EMAIL belum diatur di .env. Sinkronisasi dibatalkan.');
            return self::FAILURE;
        }

        $service = new GoogleClassroomService(config('services.google_classroom.impersonate_email'));

        $bulan = now()->format('Y-m');
        $awalBulan = now()->startOfMonth();
        $akhirBulan = now()->endOfMonth();

        $kelasList = SekolahKelas::whereNotNull('classroom_url')
            ->with(['sekolah:id,nama', 'mapel:id,nama'])
            ->get();

        if ($kelasList->isEmpty()) {
            $this->warn('Belum ada kelas dengan link Classroom yang diisi. Tidak ada yang disinkronkan.');
            return self::SUCCESS;
        }

        foreach ($kelasList as $kelas) {
            $courseId = $kelas->googleCourseId();

            if (!$courseId) {
                $this->warn("Lewati {$kelas->sekolah->nama} - {$kelas->mapel->nama}: link Classroom tidak valid.");
                continue;
            }

            try {
                $jumlahGuru   = $service->countTeachers($courseId);
                $jumlahSiswa  = $service->countStudents($courseId);
                $jumlahTask   = $service->countTaskInRange($courseId, $awalBulan, $akhirBulan);
                $jumlahMateri = $service->countMateriUploadInRange($courseId, $awalBulan, $akhirBulan);

                DB::table('classroom_kelas_stats')->updateOrInsert(
                    ['sekolah_kelas_id' => $kelas->id, 'bulan' => $bulan],
                    [
                        'jumlah_guru'   => $jumlahGuru,
                        'jumlah_siswa'  => $jumlahSiswa,
                        'jumlah_task'   => $jumlahTask,
                        'jumlah_materi' => $jumlahMateri,
                        'synced_at'     => now(),
                    ]
                );

                $this->info("Sinkron: {$kelas->sekolah->nama} - {$kelas->mapel->nama} — guru:{$jumlahGuru} siswa:{$jumlahSiswa} task:{$jumlahTask} materi:{$jumlahMateri}");
            } catch (\Throwable $e) {
                $this->error("Gagal sinkron {$kelas->sekolah->nama} - {$kelas->mapel->nama}: " . $e->getMessage());
                // lanjut ke kelas berikutnya, jangan hentikan seluruh proses
            }
        }

        return self::SUCCESS;
    }
}