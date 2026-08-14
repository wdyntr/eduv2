<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Materi;


class MateriApiController extends Controller
{
    private function guardKontenAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('materi.kelola'), 403, 'Anda tidak memiliki akses untuk mengelola konten.');
    }


    public function store(Request $request)
    {
        $this->guardKontenAccess($request);
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::create($data);
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::findOrFail($id)->update($data);
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        Materi::destroy($id);
        return response()->json(['ok' => true]);
    }
}