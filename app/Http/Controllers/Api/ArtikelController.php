<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\ArtikelKategori;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 12);
        $page  = $request->get('page', 1);
        $sort  = $request->get('sort', 'terbaru');

        $query = Artikel::with('kategori')->where('is_active', 1);

        if ($request->kategori) {
            $query->whereHas('kategori', fn($q) => $q->where('slug', $request->kategori));
        }
        if ($request->tipe) {
            $query->where('tipe', $request->tipe); // 'artikel' atau 'video'
        }
        if ($request->q) {
            $query->where('judul', 'like', "%{$request->q}%");
        }

        $orderMap = [
            'terbaru' => ['created_at', 'desc'],
            'terlama' => ['created_at', 'asc'],
            'az'      => ['judul', 'asc'],
            'za'      => ['judul', 'desc'],
        ];
        [$col, $dir] = $orderMap[$sort] ?? ['created_at', 'desc'];
        $query->orderBy($col, $dir);

        $total = $query->count();
        $items = $query->skip(($page - 1) * $limit)->take($limit)->get()->map(fn($a) => [
            'id' => $a->id,
            'judul' => $a->judul,
            'slug' => $a->slug,
            'tipe' => $a->tipe,
            'thumbnail' => $a->thumbnail,
            'kategori' => $a->kategori?->nama,
            'kategori_slug' => $a->kategori?->slug,
            'created_at' => $a->created_at,
        ]);

        return response()->json(['items' => $items, 'total' => $total]);
    }

    /** Daftar kategori + jumlah artikel aktif per kategori, buat grid kategori di halaman publik. */
    public function kategori()
    {
        $items = ArtikelKategori::withCount(['artikel' => fn($q) => $q->where('is_active', 1)])
            ->orderBy('nama')
            ->get()
            ->map(fn($k) => [
                'nama' => $k->nama,
                'slug' => $k->slug,
                'jumlah_artikel' => $k->artikel_count,
            ]);

        return response()->json(['items' => $items]);
    }

    public function show(string $slug)
    {
        $artikel = Artikel::with('kategori')->where('slug', $slug)->where('is_active', 1)->firstOrFail();

        return response()->json([
            'id' => $artikel->id,
            'judul' => $artikel->judul,
            'slug' => $artikel->slug,
            'tipe' => $artikel->tipe,
            'video_url' => $artikel->video_url,
            'konten' => $artikel->konten,
            'thumbnail' => $artikel->thumbnail,
            'kategori' => $artikel->kategori?->nama,
            'kategori_slug' => $artikel->kategori?->slug,
            'created_at' => $artikel->created_at,
        ]);
    }
}