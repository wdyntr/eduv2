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

        $data = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe'      => 'required|in:video,ppt',
            'mapel_id'  => 'required|integer|exists:mata_pelajaran,id',
            'url'       => 'required|url|max:500',
            'jenjang'   => 'required|exists:jenjang,kode',
        ]);

        $mapel = \App\Models\MataPelajaran::with('jenjang')
            ->findOrFail($data['mapel_id']);

        if ($mapel->jenjang?->kode !== $data['jenjang']) {
            return response()->json([
                'detail' => 'Mata pelajaran tidak sesuai dengan jenjang yang dipilih.'
            ], 422);
        }

        $data['thumbnail'] = Materi::resolveThumbnail(
            $data['tipe'],
            $data['url']
        );

        unset($data['jenjang']);

        Materi::create($data);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->guardKontenAccess($request);

        $data = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe'      => 'required|in:video,ppt',
            'mapel_id'  => 'required|integer|exists:mata_pelajaran,id',
            'url'       => 'required|url|max:500',
            'jenjang'   => 'required|exists:jenjang,kode',
        ]);

        $mapel = \App\Models\MataPelajaran::with('jenjang')
            ->findOrFail($data['mapel_id']);

        if ($mapel->jenjang?->kode !== $data['jenjang']) {
            return response()->json([
                'detail' => 'Mata pelajaran tidak sesuai dengan jenjang yang dipilih.'
            ], 422);
        }

        $data['thumbnail'] = Materi::resolveThumbnail(
            $data['tipe'],
            $data['url']
        );

        unset($data['jenjang']);

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