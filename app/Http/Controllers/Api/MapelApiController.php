<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Jenjang;


class MapelApiController extends Controller
{
     private function guardKontenAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('materi.kelola'), 403, 'Anda tidak memiliki akses untuk mengelola konten.');
    }
    
    public function store(Request $request)
    {
        $this->guardKontenAccess($request);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|in:sma,smk,slb',
        ]);

        $jenjang = Jenjang::where('kode', $request->jenjang)->first();

        if (!$jenjang) {
            return response()->json([
                'detail' => 'Jenjang tidak ditemukan.'
            ], 400);
        }

        $exists = MataPelajaran::where('nama', $request->nama)
            ->where('jenjang_id', $jenjang->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'detail' => 'Mata pelajaran sudah ada untuk jenjang ini.'
            ], 400);
        }

        MataPelajaran::create([
            'nama' => $request->nama,
            'jenjang_id' => $jenjang->id,
        ]);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->guardKontenAccess($request);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|in:sma,smk,slb',
        ]);

        $mapel = MataPelajaran::findOrFail($id);

        $jenjang = Jenjang::where('kode', $request->jenjang)->first();

        if (!$jenjang) {
            return response()->json([
                'detail' => 'Jenjang tidak ditemukan.'
            ], 400);
        }

        $exists = MataPelajaran::where('nama', $request->nama)
            ->where('jenjang_id', $jenjang->id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'detail' => 'Mata pelajaran sudah ada untuk jenjang ini.'
            ], 400);
        }

        $mapel->update([
            'nama' => $request->nama,
            'jenjang_id' => $jenjang->id,
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        $cnt = Materi::where('mapel_id', $id)->count();
        if ($cnt > 0) {
            return response()->json(['detail' => "Tidak bisa dihapus, masih ada {$cnt} materi yang menggunakan mata pelajaran ini."], 400);
        }
        MataPelajaran::destroy($id);
        return response()->json(['ok' => true]);
    }
}