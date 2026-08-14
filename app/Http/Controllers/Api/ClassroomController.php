<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $bulan = now()->format('Y-m');

        $query = Sekolah::where('is_active', 1);

        if ($request->filled('search')) {
            $query->where(
                'nama',
                'like',
                '%' . $request->search . '%'
            );
        }

        if ($request->filled('jenjang')) {
            $query->whereHas(
                'jenjang',
                fn ($q) => $q->where('kode', $request->jenjang)
            );
        }

        $query->withCount([
            'kelas as kelas_terisi' => function ($q) {
                $q->whereNotNull('classroom_url')
                    ->where('classroom_url', '!=', '');
            }
        ]);

        $query->withSum([
            'kelas as total_task' => function ($q) use ($bulan) {
                $q->join(
                    'classroom_kelas_stats',
                    'classroom_kelas_stats.sekolah_kelas_id',
                    '=',
                    'sekolah_kelas.id'
                )->where(
                    'classroom_kelas_stats.bulan',
                    $bulan
                );
            }
        ], 'classroom_kelas_stats.jumlah_task');

        $query->withSum([
            'kelas as total_materi' => function ($q) use ($bulan) {
                $q->join(
                    'classroom_kelas_stats',
                    'classroom_kelas_stats.sekolah_kelas_id',
                    '=',
                    'sekolah_kelas.id'
                )->where(
                    'classroom_kelas_stats.bulan',
                    $bulan
                );
            }
        ], 'classroom_kelas_stats.jumlah_materi');

        $query->with([
            'jenjang:id,kode,nama',
            'kotaKabupaten:id,nama'
        ]);

        $query->orderBy(
            'nama',
            $request->get('sort', 'az') === 'za'
                ? 'desc'
                : 'asc'
        );

        $total = (clone $query)->count();

        $limit = (int) $request->get('limit', 0);
        $page  = max(1, (int) $request->get('page', 1));

        if ($limit > 0) {
            $limit = min($limit, 100);
            $query->forPage($page, $limit);
        }

        $items = $query
            ->get([
                'id',
                'nama',
                'jenjang_id',
                'kota_kabupaten_id'
            ])
            ->map(fn ($s) => [
                'id' => $s->id,
                'nama' => $s->nama,
                'jenjang' => $s->jenjang?->kode,
                'kota_kabupaten' => $s->kotaKabupaten?->nama,
                'kelas_terisi' => $s->kelas_terisi ?? 0,
                'total_task' => $s->total_task ?? 0,
                'total_materi' => $s->total_materi ?? 0,
            ])
            ->values();

        return response()->json([
            'items' => $items,
            'total' => $total,
            'bulan' => $bulan,
        ]);
    }

    /** Detail 1 sekolah + daftar mapel (sesuai jenjang) beserta link classroom-nya (kalau ada) */
    public function show(int $id)
    {
        $sekolah = Sekolah::with([
            'jenjang:id,kode,nama',
            'kotaKabupaten:id,nama'
        ])
            ->where('is_active', 1)
            ->findOrFail($id);

        $mapel = MataPelajaran::where(
            'jenjang_id',
            $sekolah->jenjang_id
        )
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $kelas = $sekolah->kelas()
            ->pluck('classroom_url', 'mapel_id');

        $items = $mapel->map(fn ($m) => [
            'mapel_id' => $m->id,
            'nama' => $m->nama,
            'classroom_url' => $kelas[$m->id] ?? null,
        ]);

        return response()->json([
            'sekolah' => [
                'id' => $sekolah->id,
                'nama' => $sekolah->nama,
                'jenjang' => [
                    'id' => $sekolah->jenjang?->id,
                    'kode' => $sekolah->jenjang?->kode,
                    'nama' => $sekolah->jenjang?->nama,
                ],
                'kota_kabupaten' => [
                    'id' => $sekolah->kotaKabupaten?->id,
                    'nama' => $sekolah->kotaKabupaten?->nama,
                ],
            ],
            'kelas' => $items,
        ]);
    }
}