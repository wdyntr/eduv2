<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 12);
        $page  = $request->get('page', 1);
        $sort  = $request->get('sort', 'terbaru');

        $query = Materi::with('mapel')->where('is_active', 1);

        if ($request->jenjang) $query->where('jenjang', $request->jenjang);
        if ($request->tipe)    $query->where('tipe', $request->tipe);
        if ($request->mapel)   $query->whereHas('mapel', fn($q) => $q->whereRaw('LOWER(nama) = ?', [$request->mapel]));
        if ($request->q)       $query->where('judul', 'like', "%{$request->q}%");

        $orderMap = ['terbaru' => ['created_at', 'desc'], 'terlama' => ['created_at', 'asc'], 'az' => ['judul', 'asc'], 'za' => ['judul', 'desc']];
        [$col, $dir] = $orderMap[$sort] ?? ['created_at', 'desc'];
        $query->orderBy($col, $dir);

        $total = $query->count();
        $items = $query->skip(($page - 1) * $limit)->take($limit)->get()->map(function ($m) {
            return [
                'id' => $m->id, 'judul' => $m->judul, 'deskripsi' => $m->deskripsi,
                'tipe' => $m->tipe, 'jenjang' => $m->jenjang, 'url' => $m->url,
                'thumbnail' => $m->thumbnail, 'created_at' => $m->created_at,
                'mata_pelajaran' => $m->mapel?->nama,
            ];
        });

        $stats = Materi::where('is_active', 1)->selectRaw('tipe, COUNT(*) as cnt')->groupBy('tipe')->pluck('cnt', 'tipe');
        $perJenjang = Materi::where('is_active', 1)->selectRaw('jenjang, COUNT(*) as cnt')->groupBy('jenjang')->pluck('cnt', 'jenjang');

        return response()->json(['items' => $items, 'total' => $total, 'stats' => $stats, 'per_jenjang' => $perJenjang]);
    }
}