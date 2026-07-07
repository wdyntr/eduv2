<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\JurnalKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JurnalApiController extends Controller
{
    // =====================
    // PUBLIK
    // =====================

    public function index(Request $request)
    {
        $limit = $request->get('limit', 12);
        $page  = $request->get('page', 1);

        $query = Jurnal::where('status', 'approved');

        if ($request->kategori) $query->where('kategori', $request->kategori);
        if ($request->q) {
            $query->where(function ($sub) use ($request) {
                $sub->where('judul', 'like', "%{$request->q}%")
                    ->orWhere('penulis', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('reviewed_at', 'desc');

        $total = $query->count();
        $items = $query->skip(($page - 1) * $limit)->take($limit)->get()
            ->map(fn($j) => $this->publicFormat($j));

        $perKategori = Jurnal::where('status', 'approved')
            ->selectRaw('kategori, COUNT(*) as cnt')->groupBy('kategori')->pluck('cnt', 'kategori');

        return response()->json(['items' => $items, 'total' => $total, 'per_kategori' => $perKategori]);
    }

    public function show(int $id)
    {
        $jurnal = Jurnal::where('status', 'approved')->findOrFail($id);
        return response()->json($this->publicFormat($jurnal, true));
    }

    public function kategori()
    {
        return response()->json(['items' => JurnalKategori::orderBy('nama')->pluck('nama')]);
    }

    private function publicFormat(Jurnal $j, bool $detail = false): array
    {
        $data = [
            'id' => $j->id,
            'judul' => $j->judul,
            'kategori' => $j->kategori,
            'penulis' => $j->penulis,
            'abstrak' => $j->abstrak,
            'created_at' => $j->reviewed_at ?? $j->created_at,
        ];
        if ($detail) {
            $data['file_jurnal'] = asset('uploads/jurnal/' . basename($j->file_jurnal));
            $data['jumlah_halaman'] = $j->jumlah_halaman;
            $data['tahun_terbit'] = $j->tahun_terbit;
            $data['volume'] = $j->volume;
            $data['nomor_edisi'] = $j->nomor_edisi;
            $data['issn'] = $j->issn;
            $data['kata_kunci'] = $j->kata_kunci;
            $data['bahasa'] = $j->bahasa;
        }
        return $data;
    }

    // =====================
    // PENULIS (role: penulis) & ADMIN (role: admin)
    // =====================

    public function mine(Request $request)
    {
        $items = Jurnal::where('admin_id', $request->admin_session->admin_id)
            ->orderBy('created_at', 'desc')->get();
        return response()->json(['items' => $items]);
    }

    public function pending(Request $request)
    {
        $this->assertAdmin($request);
        $items = Jurnal::with(['penulisAkun'])->where('status', 'pending')
            ->orderBy('created_at', 'asc')->get()
            ->map(function ($j) {
                $j->nama_pengaju = $j->penulisAkun?->nama ?? $j->penulisAkun?->username;
                return $j;
            });
        return response()->json(['items' => $items]);
    }

    public function allAdmin(Request $request)
    {
        $this->assertAdmin($request);
        $query = Jurnal::with(['penulisAkun', 'reviewer']);
        if ($request->status) $query->where('status', $request->status);
        $items = $query->orderBy('created_at', 'desc')->get()->map(function ($j) {
            $j->nama_pengaju = $j->penulisAkun?->nama ?? $j->penulisAkun?->username;
            $j->nama_reviewer = $j->reviewer?->nama ?? $j->reviewer?->username;
            return $j;
        });
        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $this->assertPenulis($request);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100|exists:jurnal_kategori,nama',
            'penulis' => 'required|string|max:255',
            'abstrak' => 'nullable|string',
            'jumlah_halaman' => 'required|integer|min:1',
            'tahun_terbit' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'kata_kunci' => 'nullable|string|max:255',
            'bahasa' => 'nullable|string|max:30',
            'file_jurnal' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'file_bukti_plagiarisme' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $pathJurnal = $this->simpanFile($request->file('file_jurnal'));
        $pathBukti  = $this->simpanFile($request->file('file_bukti_plagiarisme'));

        $jurnal = Jurnal::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'penulis' => $request->penulis,
            'abstrak' => $request->abstrak,
            'jumlah_halaman' => $request->jumlah_halaman,
            'tahun_terbit' => $request->tahun_terbit,
            'kata_kunci' => $request->kata_kunci,
            'bahasa' => $request->bahasa ?? 'Indonesia',
            'file_jurnal' => $pathJurnal,
            'file_bukti_plagiarisme' => $pathBukti,
            'status' => 'pending',
            'admin_id' => $request->admin_session->admin_id,
        ]);

        return response()->json(['ok' => true, 'id' => $jurnal->id]);
    }

    public function resubmit(Request $request, int $id)
    {
        $this->assertPenulis($request);

        $jurnal = Jurnal::where('admin_id', $request->admin_session->admin_id)
            ->where('status', 'rejected')->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100|exists:jurnal_kategori,nama',
            'penulis' => 'required|string|max:255',
            'abstrak' => 'nullable|string',
            'jumlah_halaman' => 'required|integer|min:1',
            'tahun_terbit' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'kata_kunci' => 'nullable|string|max:255',
            'bahasa' => 'nullable|string|max:30',
            'file_jurnal' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'file_bukti_plagiarisme' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only([
            'judul', 'kategori', 'penulis', 'abstrak',
            'jumlah_halaman', 'tahun_terbit', 'kata_kunci', 'bahasa',
        ]);
        if ($request->hasFile('file_jurnal')) {
            Jurnal::hapusFileFisik($jurnal->file_jurnal);
            $data['file_jurnal'] = $this->simpanFile($request->file('file_jurnal'));
        }
        if ($request->hasFile('file_bukti_plagiarisme')) {
            Jurnal::hapusFileFisik($jurnal->file_bukti_plagiarisme);
            $data['file_bukti_plagiarisme'] = $this->simpanFile($request->file('file_bukti_plagiarisme'));
        }
        $data['status'] = 'pending';
        $data['catatan_admin'] = null;
        $data['reviewed_by'] = null;
        $data['reviewed_at'] = null;

        $jurnal->update($data);

        return response()->json(['ok' => true]);
    }

    public function approve(Request $request, int $id)
    {
        $this->assertAdmin($request);

        $request->validate([
            'volume' => 'nullable|string|max:50',
            'nomor_edisi' => 'nullable|string|max:50',
            'issn' => 'nullable|string|max:50',
        ]);

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->update([
            'status' => 'approved',
            'catatan_admin' => null,
            'reviewed_by' => $request->admin_session->admin_id,
            'reviewed_at' => now(),
            'volume' => $request->volume,
            'nomor_edisi' => $request->nomor_edisi,
            'issn' => $request->issn,
        ]);
        return response()->json(['ok' => true]);
    }

    public function updateDetail(Request $request, int $id)
    {
        $this->assertAdmin($request);

        $request->validate([
            'volume' => 'nullable|string|max:50',
            'nomor_edisi' => 'nullable|string|max:50',
            'issn' => 'nullable|string|max:50',
        ]);

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->update($request->only(['volume', 'nomor_edisi', 'issn']));

        return response()->json(['ok' => true]);
    }

    public function reject(Request $request, int $id)
    {
        $this->assertAdmin($request);
        $request->validate(['catatan' => 'required|string|max:1000']);

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan,
            'reviewed_by' => $request->admin_session->admin_id,
            'reviewed_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->assertAdmin($request);
        Jurnal::destroy($id);
        return response()->json(['ok' => true]);
    }

    // =====================
    // ADMIN — KELOLA KATEGORI
    // =====================

    public function kategoriAdminList(Request $request)
    {
        $this->assertAdmin($request);
        $items = JurnalKategori::orderBy('nama')->get()->map(function ($k) {
            $k->jumlah_jurnal = Jurnal::where('kategori', $k->nama)->count();
            return $k;
        });
        return response()->json(['items' => $items]);
    }

    public function kategoriStore(Request $request)
    {
        $this->assertAdmin($request);
        $request->validate(['nama' => 'required|string|max:100|unique:jurnal_kategori,nama']);
        JurnalKategori::create(['nama' => $request->nama]);
        return response()->json(['ok' => true]);
    }

    public function kategoriUpdate(Request $request, int $id)
    {
        $this->assertAdmin($request);
        $request->validate(['nama' => 'required|string|max:100|unique:jurnal_kategori,nama,' . $id]);

        $kategori = JurnalKategori::findOrFail($id);
        $namaLama = $kategori->nama;
        $kategori->update(['nama' => $request->nama]);

        // sinkronkan nama kategori di jurnal yang sudah memakainya
        Jurnal::where('kategori', $namaLama)->update(['kategori' => $request->nama]);

        return response()->json(['ok' => true]);
    }

    public function kategoriDestroy(Request $request, int $id)
    {
        $this->assertAdmin($request);
        $kategori = JurnalKategori::findOrFail($id);

        $cnt = Jurnal::where('kategori', $kategori->nama)->count();
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

    private function assertAdmin(Request $request): void
    {
        abort_if(($request->admin_role ?? 'admin') !== 'admin', 403, 'Hanya admin yang bisa melakukan aksi ini.');
    }

    private function assertPenulis(Request $request): void
    {
        abort_if(($request->admin_role ?? 'admin') !== 'penulis', 403, 'Hanya akun penulis yang bisa mengajukan jurnal.');
    }
}
