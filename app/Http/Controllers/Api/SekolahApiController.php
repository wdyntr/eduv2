<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\Jenjang;
use App\Models\KotaKabupaten;

class SekolahApiController extends Controller
{
    private function guardSistemAccess(Request $request): void
    {
        abort_unless(
            $request->user()?->can('sistem.kelola') || $request->user()?->can('classroom.kelola'),
            403,
            'Anda tidak memiliki akses untuk mengelola data sekolah.'
        );
    }

    private function resolveSekolahData(Request $request): array
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',

            'jenjang' => 'nullable|string|exists:jenjang,kode',
            'jenjang_id' => 'nullable|integer|exists:jenjang,id',

            'kota_kabupaten' => 'required_without:kota_kabupaten_id|nullable|string|max:150',
            'kota_kabupaten_id' => 'required_without:kota_kabupaten|nullable|integer|exists:kota_kabupaten,id',
        ]);

        /*
         * Resolve jenjang
         */
        if (
            empty($data['jenjang_id']) &&
            !empty($data['jenjang'])
        ) {
            $data['jenjang_id'] = Jenjang::where(
                'kode',
                $data['jenjang']
            )->value('id');
        }

        if (empty($data['jenjang_id'])) {
            abort(422, 'Jenjang wajib dipilih.');
        }

        /*
         * Resolve kota/kabupaten
         */
        if (
            empty($data['kota_kabupaten_id']) &&
            !empty($data['kota_kabupaten'])
        ) {
            $kotaId = KotaKabupaten::whereRaw(
                'LOWER(nama) = ?',
                [strtolower(trim($data['kota_kabupaten']))]
            )->value('id');

            if (!$kotaId) {
                abort(
                    422,
                    'Kota/Kabupaten yang dipilih tidak terdaftar di database.'
                );
            }

            $data['kota_kabupaten_id'] = $kotaId;
        }

        return [
            'nama' => trim($data['nama']),
            'jenjang_id' => $data['jenjang_id'],
            'kota_kabupaten_id' => $data['kota_kabupaten_id'] ?? null,
        ];
    }

    /**
     * Daftar sekolah
     */
    public function index(Request $request)
    {
        $this->guardSistemAccess($request);

        $query = Sekolah::with([
            'jenjang:id,kode,nama',
            'kotaKabupaten:id,nama',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(
                'nama',
                'like',
                "%{$search}%"
            );
        }

        if ($request->filled('jenjang')) {
            $query->whereHas('jenjang', function ($q) use ($request) {
                $q->where('kode', $request->jenjang);
            });
        }

        $paginator = $query
            ->orderBy('nama')
            ->paginate(
                $request->integer('limit', 10)
            );

        $items = $paginator->getCollection()->map(function ($sekolah) {
            return [
                'id' => $sekolah->id,
                'nama' => $sekolah->nama,

                'jenjang' => $sekolah->jenjang?->kode,
                'jenjang_nama' => $sekolah->jenjang?->nama,

                'kota_kabupaten' => $sekolah->kotaKabupaten?->nama,

                'kelas_terisi' => $sekolah->kelas()
                    ->whereNotNull('classroom_url')
                    ->where('classroom_url', '!=', '')
                    ->count(),

                // Untuk sementara statistik ini bisa 0/null
                // sampai statistik Classroom benar-benar diimplementasikan.
                'total_task' => 0,
                'total_materi' => 0,
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * Detail sekolah
     */
    public function show(Request $request, int $id)
    {
        $this->guardSistemAccess($request);

        $sekolah = Sekolah::with([
            'jenjang:id,kode,nama',
            'kotaKabupaten:id,nama',
        ])->findOrFail($id);

        return response()->json([
            'sekolah' => [
                'id' => $sekolah->id,
                'nama' => $sekolah->nama,
                'jenjang' => $sekolah->jenjang?->kode,
                'jenjang_nama' => $sekolah->jenjang?->nama,
                'kota_kabupaten' => $sekolah->kotaKabupaten?->nama,
            ],
        ]);
    }

    /**
     * Tambah sekolah
     */
    public function store(Request $request)
    {
        $this->guardSistemAccess($request);

        $sekolah = Sekolah::create(
            $this->resolveSekolahData($request)
        );

        return response()->json([
            'ok' => true,
            'sekolah' => $sekolah,
        ], 201);
    }

    /**
     * Update sekolah
     */
    public function update(Request $request, int $id)
    {
        $this->guardSistemAccess($request);

        $sekolah = Sekolah::findOrFail($id);

        $sekolah->update(
            $this->resolveSekolahData($request)
        );

        return response()->json([
            'ok' => true,
            'sekolah' => $sekolah->fresh(),
        ]);
    }

    /**
     * Hapus sekolah
     */
    public function destroy(Request $request, int $id)
    {
        $this->guardSistemAccess($request);

        $sekolah = Sekolah::findOrFail($id);

        $jumlahKelasTerisi = $sekolah->kelas()
            ->whereNotNull('classroom_url')
            ->where('classroom_url', '!=', '')
            ->count();

        if ($jumlahKelasTerisi > 0) {
            return response()->json([
                'detail' =>
                    "Tidak bisa dihapus, sekolah ini masih memiliki {$jumlahKelasTerisi} kelas Classroom yang terisi. Kosongkan dulu semua link Classroom-nya sebelum menghapus sekolah.",
            ], 400);
        }

        $sekolah->delete();

        return response()->json([
            'ok' => true,
        ]);
    }
}
