<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\Materi;


class MapelApiController extends Controller
{
     private function guardKontenAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('materi.kelola'), 403, 'Anda tidak memiliki akses untuk mengelola konten.');
    }
    
    public function store(Request $request)
    {
        $this->guardKontenAccess($request);
        try {
            MataPelajaran::create($request->only(['nama', 'jenjang_id']));
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Mata pelajaran sudah ada untuk jenjang ini.'], 400);
        }
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        MataPelajaran::findOrFail($id)->update($request->only(['nama', 'jenjang_id']));
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