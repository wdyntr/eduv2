<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\ArtikelKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtikelApiController extends Controller
{
    private function guardArtikelAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('artikel.kelola'), 403, 'Anda tidak memiliki akses untuk mengelola artikel.');
    }

    public function index(Request $request)
    {
        $this->guardArtikelAccess($request);

        $limit = $request->get('limit', 10);
        $page  = $request->get('page', 1);

        $query = Artikel::with('kategori');
        if ($request->q) {
            $query->where('judul', 'like', "%{$request->q}%");
        }
        $query->orderByDesc('created_at');

        $total = $query->count();
        $items = $query->skip(($page - 1) * $limit)->take($limit)->get()->map(fn($a) => [
            'id' => $a->id,
            'judul' => $a->judul,
            'tipe' => $a->tipe,
            'kategori' => $a->kategori?->nama,
            'is_active' => $a->is_active,
            'created_at' => $a->created_at,
        ]);

        return response()->json(['items' => $items, 'total' => $total]);
    }

    public function show(Request $request, int $id)
    {
        $this->guardArtikelAccess($request);
        return response()->json(Artikel::findOrFail($id));
    }

    private function rules(): array
    {
        return [
            'judul'       => 'required|string|max:255',
            'kategori_id' => 'required|integer|exists:artikel_kategori,id',
            'tipe'        => 'required|in:artikel,video',
            'video_url'   => 'nullable|required_if:tipe,video|url|max:500',
            'konten'      => 'required|string',
            'thumbnail'   => 'nullable|url|max:500',
            'is_active'   => 'nullable|boolean',
        ];
    }

    public function store(Request $request)
    {
        $this->guardArtikelAccess($request);

        $data = $request->validate($this->rules());
        $data['thumbnail'] = Artikel::normalizeThumbnailUrl($data['thumbnail'] ?? null);
        $data['slug'] = Artikel::buatSlugUnik($data['judul']);
        $data['is_active'] = $data['is_active'] ?? true;

        Artikel::create($data);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->guardArtikelAccess($request);

        $data = $request->validate($this->rules());
        $data['thumbnail'] = Artikel::normalizeThumbnailUrl($data['thumbnail'] ?? null);
        $artikel = Artikel::findOrFail($id);

        if ($data['thumbnail'] !== $artikel->thumbnail) {
            $this->hapusThumbnailCache($artikel->thumbnail);
        }

        if ($data['judul'] !== $artikel->judul) {
            $data['slug'] = Artikel::buatSlugUnik($data['judul'], $id);
        }
        $data['is_active'] = $data['is_active'] ?? true;

        $artikel->update($data);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->guardArtikelAccess($request);

        $artikel = Artikel::findOrFail($id);
        $this->hapusThumbnailCache($artikel->thumbnail);
        $artikel->delete();

        return response()->json(['ok' => true]);
    }

    private function hapusThumbnailCache(?string $thumbnailUrl): void
    {
        $id = Artikel::extractProxyId($thumbnailUrl);
        if (!$id) return;

        $path = "thumbnail-cache/{$id}.jpg";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // =====================
    // KELOLA KATEGORI
    // =====================

    public function kategoriList(Request $request)
    {
        $this->guardArtikelAccess($request);

        $items = ArtikelKategori::withCount('artikel')
            ->orderBy('nama')
            ->get()
            ->map(fn($k) => [
                'id' => $k->id,
                'nama' => $k->nama,
                'jumlah_artikel' => $k->artikel_count,
            ]);

        return response()->json(['items' => $items]);
    }

    public function kategoriStore(Request $request)
    {
        $this->guardArtikelAccess($request);
        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:artikel_kategori,nama',
        ]);
        $data['slug'] = ArtikelKategori::buatSlugUnik($data['nama']);
        ArtikelKategori::create($data);
        return response()->json(['ok' => true]);
    }

    public function kategoriUpdate(Request $request, int $id)
    {
        $this->guardArtikelAccess($request);
        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:artikel_kategori,nama,' . $id,
        ]);

        $kategori = ArtikelKategori::findOrFail($id);
        if ($data['nama'] !== $kategori->nama) {
            $data['slug'] = ArtikelKategori::buatSlugUnik($data['nama'], $id);
        }
        $kategori->update($data);

        return response()->json(['ok' => true]);
    }

    public function kategoriDestroy(Request $request, int $id)
    {
        $this->guardArtikelAccess($request);
        $kategori = ArtikelKategori::findOrFail($id);

        $cnt = Artikel::where('kategori_id', $id)->count();
        if ($cnt > 0) {
            return response()->json(['detail' => "Tidak bisa dihapus, masih ada {$cnt} artikel di kategori ini."], 400);
        }

        $kategori->delete();
        return response()->json(['ok' => true]);
    }
}