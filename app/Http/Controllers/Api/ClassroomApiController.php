<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;

class ClassroomApiController extends Controller
{
    private function guardClassroomAccess(Request $request): void
    {
        abort_unless(
            $request->user()?->can('classroom.kelola'),
            403,
            'Anda tidak memiliki akses untuk mengelola Classroom.'
        );
    }

    private function assertSekolahAccess(
        Request $request,
        int $sekolahId
    ): void {
        $user = $request->user();

        /*
         * User global boleh mengakses semua sekolah.
         * User yang punya sekolah_id hanya boleh sekolahnya sendiri.
         */
        if (!$user?->sekolah_id) {
            return;
        }

        abort_if(
            (int) $user->sekolah_id !== $sekolahId,
            403,
            'Anda tidak memiliki akses ke data sekolah ini.'
        );
    }

    /**
     * Daftar kelas Classroom berdasarkan sekolah.
     */
    public function kelas(Request $request, int $id)
    {
        $this->guardClassroomAccess($request);
        $this->assertSekolahAccess($request, $id);

        $bulan = now()->format('Y-m');

        $sekolah = Sekolah::with([
            'jenjang:id,kode,nama',
            'kotaKabupaten:id,nama',
        ])->findOrFail($id);

        $mapel = MataPelajaran::where(
            'jenjang_id',
            $sekolah->jenjang_id
        )
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $kelas = $sekolah
            ->kelas()
            ->get([
                'id',
                'mapel_id',
                'classroom_url',
            ])
            ->keyBy('mapel_id');

        $statsByKelasId = DB::table('classroom_kelas_stats')
            ->whereIn(
                'sekolah_kelas_id',
                $kelas->pluck('id')
            )
            ->where('bulan', $bulan)
            ->get()
            ->keyBy('sekolah_kelas_id');

        $items = $mapel->map(function ($m) use (
            $kelas,
            $statsByKelasId
        ) {
            $k = $kelas[$m->id] ?? null;

            $stat = $k
                ? ($statsByKelasId[$k->id] ?? null)
                : null;

            return [
                'mapel_id' => $m->id,
                'nama' => $m->nama,
                'classroom_url' => $k?->classroom_url,

                'jumlah_guru' => $stat?->jumlah_guru,
                'jumlah_siswa' => $stat?->jumlah_siswa,
                'jumlah_task' => $stat?->jumlah_task,
                'jumlah_materi' => $stat?->jumlah_materi,

                'synced_at' => $stat?->synced_at,
            ];
        });

        return response()->json([
            'sekolah' => [
                'id' => $sekolah->id,
                'nama' => $sekolah->nama,
                'jenjang' => $sekolah->jenjang?->kode,
                'jenjang_nama' => $sekolah->jenjang?->nama,
                'kota_kabupaten' => $sekolah->kotaKabupaten?->nama,
            ],
            'bulan' => $bulan,
            'kelas' => $items,
        ]);
    }

    /**
     * Update link Classroom suatu mata pelajaran.
     */
    public function updateKelas(
        Request $request,
        int $id,
        int $mapelId
    ) {
        $this->guardClassroomAccess($request);
        $this->assertSekolahAccess($request, $id);

        $sekolah = Sekolah::findOrFail($id);

        $mapel = MataPelajaran::where(
            'jenjang_id',
            $sekolah->jenjang_id
        )->findOrFail($mapelId);

        $data = $request->validate([
            'classroom_url' => 'nullable|url|max:500',
        ]);

        \App\Models\SekolahKelas::updateOrCreate(
            [
                'sekolah_id' => $sekolah->id,
                'mapel_id' => $mapel->id,
            ],
            [
                'classroom_url' =>
                    $data['classroom_url'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
        ]);
    }
}
