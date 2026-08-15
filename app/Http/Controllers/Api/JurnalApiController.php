<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\JurnalKategori;
use App\Models\JurnalRevisi;
use App\Models\JurnalReview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Ditulis ulang total untuk skema baru (dulu tabel `jurnal` flat, sekarang
 * dipecah 3 tabel):
 *   - jurnal          : id, kategori_id, user_id  (satu baris = satu pengajuan jurnal)
 *   - jurnal_revisi   : versi data (judul, penulis, file, dst) per pengajuan ulang
 *   - jurnal_review   : keputusan admin (status/catatan) untuk 1 revisi
 *
 * "Status" sebuah jurnal sekarang = status milik reviewTerbaru dari revisiTerbaru-nya,
 * bukan kolom langsung. Resubmit setelah ditolak membuat revisi BARU (versi_ke+1)
 * beserta review baru berstatus pending, bukan menimpa data revisi lama —
 * supaya riwayat pengajuan sebelumnya tetap tersimpan.
 */
class JurnalApiController extends Controller
{
    // =====================
    // PUBLIK
    // =====================

    public function index(Request $request)
    {
        $limit = $request->get('limit', 12);
        $page  = $request->get('page', 1);

        $query = Jurnal::with(['kategori', 'revisiTerbaru.reviewTerbaru'])
            ->whereHas('revisiTerbaru.reviewTerbaru', fn($q) => $q->where('status', 'approved'));

        if ($request->kategori) {
            $query->whereHas('kategori', fn($q) => $q->where('nama_kategori', $request->kategori));
        }
        if ($request->q) {
            $query->whereHas('revisiTerbaru', function ($sub) use ($request) {
                $sub->where('judul', 'like', "%{$request->q}%")
                    ->orWhere('penulis', 'like', "%{$request->q}%");
            });
        }

        $all = $query->get()->sortByDesc(fn($j) => $j->revisiTerbaru?->reviewTerbaru?->reviewed_at)->values();

        $total = $all->count();
        $items = $all->slice(($page - 1) * $limit, $limit)->values()->map(fn($j) => $this->publicFormat($j));

        $perKategori = Jurnal::whereHas('revisiTerbaru.reviewTerbaru', fn($q) => $q->where('status', 'approved'))
            ->join('jurnal_kategori', 'jurnal_kategori.id', '=', 'jurnal.kategori_id')
            ->selectRaw('jurnal_kategori.nama_kategori as kategori, COUNT(*) as cnt')
            ->groupBy('jurnal_kategori.nama_kategori')
            ->pluck('cnt', 'kategori');

        return response()->json(['items' => $items, 'total' => $total, 'per_kategori' => $perKategori]);
    }

    public function show(int $id)
    {
        $jurnal = Jurnal::with(['kategori', 'revisiTerbaru.reviewTerbaru'])
            ->whereHas('revisiTerbaru.reviewTerbaru', fn($q) => $q->where('status', 'approved'))
            ->findOrFail($id);

        return response()->json($this->publicFormat($jurnal, true));
    }

    public function kategori()
    {
        return response()->json(['items' => JurnalKategori::orderBy('nama_kategori')->pluck('nama_kategori')]);
    }

    private function publicFormat(Jurnal $j, bool $detail = false): array
    {
        $revisi = $j->revisiTerbaru;
        $review = $revisi?->reviewTerbaru;

        $data = [
            'id' => $j->id,
            'judul' => $revisi?->judul,
            'kategori' => $j->kategori?->nama_kategori,
            'penulis' => $revisi?->penulis,
            'abstrak' => $revisi?->abstrak,
            'created_at' => $review?->reviewed_at ?? $j->created_at,
        ];
        if ($detail && $revisi) {
            $data['file_jurnal'] = asset('uploads/jurnal/' . basename($revisi->file_jurnal));
            $data['jumlah_halaman'] = $revisi->jumlah_halaman;
            $data['tahun_terbit'] = $revisi->tahun_terbit;
            $data['volume'] = $revisi->volume;
            $data['nomor_edisi'] = $revisi->nomor_edisi;
            $data['issn'] = $revisi->issn;
            $data['kata_kunci'] = $revisi->kata_kunci;
            $data['bahasa'] = $revisi->bahasa;
        }
        return $data;
    }

    // =====================
    // PENULIS (role: guru) & ADMIN (role: admin)
    // =====================

    public function mine(Request $request)
    {
        $items = Jurnal::with(['kategori', 'revisiTerbaru.reviewTerbaru'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')->get()
            ->map(fn($j) => $this->ownerFormat($j));
        return response()->json(['items' => $items]);
    }

    public function pending(Request $request)
    {
        $this->assertJurnalViewer($request);
        $items = Jurnal::with(['kategori', 'penulisAkun', 'revisiTerbaru.reviewTerbaru'])
            ->whereHas('revisiTerbaru.reviewTerbaru', fn($q) => $q->where('status', 'pending'))
            ->get()
            ->sortBy(fn($j) => $j->revisiTerbaru?->created_at)
            ->values()
            ->map(function ($j) {
                $data = $this->adminFormat($j);
                $data['nama_pengaju'] = $j->penulisAkun?->nama ?? $j->penulisAkun?->username;
                return $data;
            });
        return response()->json(['items' => $items]);
    }

    public function allAdmin(Request $request)
    {
        $this->assertJurnalViewer($request);
        $items = Jurnal::with(['kategori', 'penulisAkun', 'revisiTerbaru.reviewTerbaru.reviewer'])->get();

        if ($request->status) {
            $items = $items->filter(fn($j) => ($j->revisiTerbaru?->reviewTerbaru?->status ?? 'pending') === $request->status);
        }

        $items = $items->sortByDesc('created_at')->values()->map(function ($j) {
            $data = $this->adminFormat($j);
            $data['nama_pengaju'] = $j->penulisAkun?->nama ?? $j->penulisAkun?->username;
            $reviewer = $j->revisiTerbaru?->reviewTerbaru?->reviewer;
            $data['nama_reviewer'] = $reviewer?->nama ?? $reviewer?->username;
            return $data;
        })->values();

        return response()->json(['items' => $items]);
    }

    private function ownerFormat(Jurnal $j): array
    {
        $revisi = $j->revisiTerbaru;
        $review = $revisi?->reviewTerbaru;

        return [
            'id' => $j->id,
            'judul' => $revisi?->judul,
            'kategori' => $j->kategori?->nama_kategori,
            'penulis' => $revisi?->penulis,
            'abstrak' => $revisi?->abstrak,
            'kata_kunci' => $revisi?->kata_kunci,
            'jumlah_halaman' => $revisi?->jumlah_halaman,
            'tahun_terbit' => $revisi?->tahun_terbit,
            'bahasa' => $revisi?->bahasa,
            'versi_ke' => $revisi?->versi_ke,
            'status' => $review?->status ?? 'pending',
            'catatan_admin' => $review?->catatan_admin,
            'created_at' => $j->created_at,
        ];
    }

    private function adminFormat(Jurnal $j): array
    {
        $revisi = $j->revisiTerbaru;
        $review = $revisi?->reviewTerbaru;

        return [
            'id' => $j->id,
            'judul' => $revisi?->judul,
            'kategori' => $j->kategori?->nama_kategori,
            'penulis' => $revisi?->penulis,
            'abstrak' => $revisi?->abstrak,
            'kata_kunci' => $revisi?->kata_kunci,
            'jumlah_halaman' => $revisi?->jumlah_halaman,
            'tahun_terbit' => $revisi?->tahun_terbit,
            'bahasa' => $revisi?->bahasa,
            'volume' => $revisi?->volume,
            'nomor_edisi' => $revisi?->nomor_edisi,
            'issn' => $revisi?->issn,
            'file_jurnal' => $revisi ? basename($revisi->file_jurnal) : null,
            'file_bukti_plagiarisme' => $revisi ? basename($revisi->file_bukti_plagiarisme) : null,
            'versi_ke' => $revisi?->versi_ke,
            'status' => $review?->status ?? 'pending',
            'catatan_admin' => $review?->catatan_admin,
            'reviewed_at' => $review?->reviewed_at,
            'created_at' => $j->created_at,
        ];
    }

    public function store(Request $request)
    {
        $this->assertPenulis($request);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100|exists:jurnal_kategori,nama_kategori',
            'penulis' => 'required|string|max:255',
            'abstrak' => 'nullable|string',
            'jumlah_halaman' => 'required|integer|min:1',
            'tahun_terbit' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'kata_kunci' => 'nullable|string|max:255',
            'bahasa' => 'nullable|string|max:30',
            'file_jurnal' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'file_bukti_plagiarisme' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $kategori = JurnalKategori::where('nama_kategori', $request->kategori)->firstOrFail();

        $pathJurnal = $this->simpanFile($request->file('file_jurnal'));
        $pathBukti  = $this->simpanFile($request->file('file_bukti_plagiarisme'));

        $jurnal = Jurnal::create([
            'kategori_id' => $kategori->id,
            'user_id' => $request->user()->id,
        ]);

        $revisi = JurnalRevisi::create([
            'jurnal_id' => $jurnal->id,
            'versi_ke' => 1,
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'abstrak' => $request->abstrak,
            'jumlah_halaman' => $request->jumlah_halaman,
            'tahun_terbit' => $request->tahun_terbit,
            'kata_kunci' => $request->kata_kunci,
            'bahasa' => $request->bahasa ?? 'Indonesia',
            'file_jurnal' => $pathJurnal,
            'file_bukti_plagiarisme' => $pathBukti,
        ]);

        JurnalReview::create([
            'jurnal_revisi_id' => $revisi->id,
            'status' => 'pending',
        ]);

        return response()->json(['ok' => true, 'id' => $jurnal->id]);
    }

    public function resubmit(Request $request, int $id)
    {
        $this->assertPenulis($request);

        $jurnal = Jurnal::with('revisiTerbaru.reviewTerbaru')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $revisiLama = $jurnal->revisiTerbaru;
        $reviewLama = $revisiLama?->reviewTerbaru;
        abort_unless($reviewLama && $reviewLama->status === 'rejected', 400, 'Jurnal ini tidak dalam status ditolak, tidak bisa diajukan ulang.');

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100|exists:jurnal_kategori,nama_kategori',
            'penulis' => 'required|string|max:255',
            'abstrak' => 'nullable|string',
            'jumlah_halaman' => 'required|integer|min:1',
            'tahun_terbit' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'kata_kunci' => 'nullable|string|max:255',
            'bahasa' => 'nullable|string|max:30',
            'file_jurnal' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'file_bukti_plagiarisme' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $kategori = JurnalKategori::where('nama_kategori', $request->kategori)->firstOrFail();
        $jurnal->update(['kategori_id' => $kategori->id]);

        $fileJurnal = $revisiLama->file_jurnal;
        if ($request->hasFile('file_jurnal')) {
            JurnalRevisi::hapusFileFisik($fileJurnal);
            $fileJurnal = $this->simpanFile($request->file('file_jurnal'));
        }

        $fileBukti = $revisiLama->file_bukti_plagiarisme;
        if ($request->hasFile('file_bukti_plagiarisme')) {
            JurnalRevisi::hapusFileFisik($fileBukti);
            $fileBukti = $this->simpanFile($request->file('file_bukti_plagiarisme'));
        }

        $revisiBaru = JurnalRevisi::create([
            'jurnal_id' => $jurnal->id,
            'versi_ke' => $revisiLama->versi_ke + 1,
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'abstrak' => $request->abstrak,
            'jumlah_halaman' => $request->jumlah_halaman,
            'tahun_terbit' => $request->tahun_terbit,
            'kata_kunci' => $request->kata_kunci,
            'bahasa' => $request->bahasa ?? 'Indonesia',
            'file_jurnal' => $fileJurnal,
            'file_bukti_plagiarisme' => $fileBukti,
        ]);

        JurnalReview::create([
            'jurnal_revisi_id' => $revisiBaru->id,
            'status' => 'pending',
        ]);

        return response()->json(['ok' => true]);
    }

    public function approve(Request $request, int $id)
    {
        $this->assertPereview($request);

        $request->validate([
            'volume' => 'nullable|string|max:50',
            'nomor_edisi' => 'nullable|string|max:50',
            'issn' => 'nullable|string|max:50',
        ]);

        $jurnal = Jurnal::with('revisiTerbaru.reviewTerbaru')->findOrFail($id);
        $revisi = $jurnal->revisiTerbaru;
        abort_unless($revisi, 404, 'Jurnal ini belum punya revisi.');

        $revisi->update($request->only(['volume', 'nomor_edisi', 'issn']));

        $this->putReviewStatus($revisi, 'approved', $request->user()->id);

        return response()->json(['ok' => true]);
    }

    public function updateDetail(Request $request, int $id)
    {
        $this->assertPereview($request);

        $request->validate([
            'volume' => 'nullable|string|max:50',
            'nomor_edisi' => 'nullable|string|max:50',
            'issn' => 'nullable|string|max:50',
        ]);

        $jurnal = Jurnal::with('revisiTerbaru')->findOrFail($id);
        abort_unless($jurnal->revisiTerbaru, 404, 'Jurnal ini belum punya revisi.');
        $jurnal->revisiTerbaru->update($request->only(['volume', 'nomor_edisi', 'issn']));

        return response()->json(['ok' => true]);
    }

    public function reject(Request $request, int $id)
    {
        $this->assertPereview($request);
        $request->validate(['catatan' => 'required|string|max:1000']);

        $jurnal = Jurnal::with('revisiTerbaru.reviewTerbaru')->findOrFail($id);
        $revisi = $jurnal->revisiTerbaru;
        abort_unless($revisi, 404, 'Jurnal ini belum punya revisi.');

        $this->putReviewStatus($revisi, 'rejected', $request->user()->id, $request->catatan);

        return response()->json(['ok' => true]);
    }

    /** Update review terbaru kalau ada, atau buat baru kalau revisi ini belum pernah direview. */
    private function putReviewStatus(JurnalRevisi $revisi, string $status, int $reviewerId, ?string $catatan = null): void
    {
        $review = $revisi->reviewTerbaru;
        $payload = [
            'status' => $status,
            'catatan_admin' => $catatan,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ];

        if ($review) {
            $review->update($payload);
        } else {
            JurnalReview::create(array_merge(['jurnal_revisi_id' => $revisi->id], $payload));
        }
    }

    public function destroy(Request $request, int $id)
    {
        $this->assertAdmin($request);
        $jurnal = Jurnal::with('revisi')->findOrFail($id);
        $jurnal->revisi->each->delete();
        $jurnal->delete();
        return response()->json(['ok' => true]);
    }

    // =====================
    // ADMIN — KELOLA KATEGORI
    // =====================

    public function kategoriAdminList(Request $request)
    {
        abort_if(
            !$request->user()?->can('sistem.kelola')
            && !$request->user()?->can('jurnal.review'),
            403,
            'Anda tidak memiliki izin untuk melihat kategori.'
        );

        $items = JurnalKategori::orderBy('nama_kategori')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nama' => $k->nama_kategori,
                    'jumlah_jurnal' => Jurnal::where('kategori_id', $k->id)->count(),
                ];
            })
            ->values();

        return response()->json(['items' => $items]);
    }

    public function kategoriStore(Request $request)
    {
        $this->assertAdmin($request);
        $request->validate(['nama' => 'required|string|max:100|unique:jurnal_kategori,nama_kategori']);
        JurnalKategori::create(['nama_kategori' => $request->nama]);
        return response()->json(['ok' => true]);
    }

    public function kategoriUpdate(Request $request, int $id)
    {
        $this->assertAdmin($request);
        $request->validate(['nama' => 'required|string|max:100|unique:jurnal_kategori,nama_kategori,' . $id]);

        JurnalKategori::findOrFail($id)->update(['nama_kategori' => $request->nama]);
        // Tidak perlu sinkronisasi manual ke tabel jurnal lagi — jurnal menyimpan
        // kategori_id (FK), bukan nama kategori, jadi otomatis ikut berubah.

        return response()->json(['ok' => true]);
    }

    public function kategoriDestroy(Request $request, int $id)
    {
        $this->assertAdmin($request);
        $kategori = JurnalKategori::findOrFail($id);

        $cnt = Jurnal::where('kategori_id', $kategori->id)->count();
        if ($cnt > 0) {
            return response()->json(['detail' => "Tidak bisa dihapus, masih ada {$cnt} jurnal yang menggunakan kategori ini."], 400);
        }

        $kategori->delete();
        return response()->json(['ok' => true]);
    }

    private function simpanFile($file): string
    {
        $dir = config('jurnal.upload_path');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return $filename;
    }

    /** Kelola kategori & hapus jurnal: admin_sistem saja */
    private function assertAdmin(Request $request): void
    {
        abort_if(
            !$request->user()?->can('sistem.kelola'),
            403,
            'Anda tidak memiliki izin untuk mengelola data sistem.'
        );
    }

    private function assertPenulis(Request $request): void
    {
        abort_if(
            !$request->user()?->can('jurnal.ajukan'),
            403,
            'Anda tidak memiliki izin untuk mengajukan jurnal.'
        );
    }

    /** Approve/reject/edit detail jurnal: hak pereview saja — admin_sistem hanya bisa melihat */
    private function assertPereview(Request $request): void
    {
        abort_if(
            !$request->user()?->can('jurnal.review'),
            403,
            'Anda tidak memiliki izin untuk melakukan review jurnal.'
        );
    }

    /** Lihat daftar jurnal (pending/semua): admin_sistem (lihat saja) + pereview */
    private function assertJurnalViewer(Request $request): void
    {
        abort_if(
            !$request->user()?->canAny(['jurnal.lihat', 'jurnal.review']),
            403,
            'Anda tidak memiliki izin untuk melihat daftar review jurnal.'
        );
    }
}