<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::with('jenjang')
            ->orderBy('nama');

        if ($request->filled('jenjang')) {
            $query->whereHas(
                'jenjang',
                fn ($q) => $q->where('kode', $request->jenjang)
            );
        }

        $items = $query->get([
            'id',
            'nama',
            'jenjang_id',
        ])->map(fn ($m) => [
            'id' => $m->id,
            'nama' => $m->nama,
            'jenjang_id' => $m->jenjang_id,
            'jenjang' => $m->jenjang?->kode,
        ]);

        return response()->json([
            'items' => $items,
            'total' => $items->count(),
        ]);
    }
}