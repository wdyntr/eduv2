<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminSession;
use App\Models\Materi;
use App\Models\Sekolah;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
    private function guardAdminOnly(Request $request): void
    {
        abort_if(($request->admin_role ?? 'admin') !== 'admin', 403, 'Hanya admin yang bisa melakukan aksi ini.');
    }

    public function login(Request $request)
    {
        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !password_verify($request->password, $admin->password)) {
            return response()->json(['detail' => 'Username atau password salah.'], 401);
        }

        $token = Str::random(64);
        AdminSession::create([
            'token' => $token,
            'admin_id' => $admin->id,
            'username' => $admin->username,
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json(['ok' => true])
            ->withCookie(\Cookie::make('admin_session', $token, 1440, '/', null, false, false));
    }

    public function getUsers(Request $request)
    {
        $this->guardAdminOnly($request);
        $items = Admin::with('sekolah:id,nama')
            ->select('id', 'username', 'nama', 'role', 'sekolah_id', 'created_at')
            ->orderBy('id')->get();
        return response()->json(['items' => $items, 'total' => $items->count()]);
    }

    public function tambahAdmin(Request $request)
    {
        $this->guardAdminOnly($request);
        $role = in_array($request->role, ['admin', 'guru', 'sekolah']) ? $request->role : 'admin';

        if ($role === 'sekolah' && !$request->sekolah_id) {
            return response()->json(['detail' => 'Akun dengan peran Sekolah wajib dikaitkan ke salah satu sekolah.'], 400);
        }

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama ?? '',
            'role' => $role,
            'sekolah_id' => $role === 'sekolah' ? $request->sekolah_id : null,
        ]);
        return response()->json(['ok' => true]);
    }

    public function hapusAdmin(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        $session = $request->admin_session;
        if ($session->admin_id == $id) {
            return response()->json(['detail' => 'Tidak bisa hapus akun sendiri.'], 400);
        }
        Admin::destroy($id);
        return response()->json(['ok' => true]);
    }

    // =====================
    // MONITORING CLASSROOM — admin (semua sekolah) & sekolah (sekolah sendiri)
    // =====================

    private function guardClassroomAccess(Request $request): void
    {
        abort_if(!in_array($request->admin_role ?? 'admin', ['admin', 'sekolah']), 403, 'Tidak memiliki akses.');
    }

    // =====================
    // KELOLA LINK CLASSROOM PER MATA PELAJARAN (halaman detail per sekolah)
    // =====================

    /** Pastikan admin bisa akses sekolah manapun, sedangkan role sekolah cuma sekolahnya sendiri */
    private function assertSekolahAccess(Request $request, int $sekolahId): void
    {
        $role = $request->admin_role ?? 'admin';
        if ($role === 'admin') return;
        if ($role === 'sekolah' && (int) $request->admin_sekolah_id === $sekolahId) return;
        abort(403, 'Anda tidak memiliki akses ke data sekolah ini.');
    }

    /** Detail sekolah + daftar mapel (sesuai jenjang) beserta link classroom & statistik bulan ini */
    public function sekolahKelasList(Request $request, int $id)
    {
        $this->guardClassroomAccess($request);
        $this->assertSekolahAccess($request, $id);

        $bulan = now()->format('Y-m');
        $sekolah = Sekolah::findOrFail($id);
        $mapel = MataPelajaran::where('jenjang', $sekolah->jenjang)->orderBy('nama')->get(['id', 'nama']);
        $kelas = $sekolah->kelas()->get(['id', 'mapel_id', 'classroom_url'])->keyBy('mapel_id');

        $statsByKelasId = DB::table('classroom_kelas_stats')
            ->whereIn('sekolah_kelas_id', $kelas->pluck('id'))
            ->where('bulan', $bulan)
            ->get()->keyBy('sekolah_kelas_id');

        $items = $mapel->map(function ($m) use ($kelas, $statsByKelasId) {
            $k = $kelas[$m->id] ?? null;
            $stat = $k ? ($statsByKelasId[$k->id] ?? null) : null;
            return [
                'mapel_id' => $m->id,
                'nama' => $m->nama,
                'classroom_url' => $k->classroom_url ?? null,
                'jumlah_guru' => $stat->jumlah_guru ?? null,
                'jumlah_siswa' => $stat->jumlah_siswa ?? null,
                'jumlah_task' => $stat->jumlah_task ?? null,
                'jumlah_materi' => $stat->jumlah_materi ?? null,
                'synced_at' => $stat->synced_at ?? null,
            ];
        });

        return response()->json(['sekolah' => $sekolah, 'bulan' => $bulan, 'kelas' => $items]);
    }

    /** Simpan/ubah link classroom untuk 1 pasang sekolah+mapel */
    public function sekolahKelasUpdate(Request $request, int $id, int $mapelId)
    {
        $this->guardClassroomAccess($request);
        $this->assertSekolahAccess($request, $id);
        abort_if(($request->admin_role ?? 'admin') !== 'sekolah', 403, 'Hanya operator sekolah yang bisa mengubah link Classroom. Admin/dinas hanya dapat memantau.');

        $sekolah = Sekolah::findOrFail($id);
        $mapel = MataPelajaran::where('jenjang', $sekolah->jenjang)->findOrFail($mapelId);

        $request->validate(['classroom_url' => 'nullable|url|max:500']);

        \App\Models\SekolahKelas::updateOrCreate(
            ['sekolah_id' => $sekolah->id, 'mapel_id' => $mapel->id],
            ['classroom_url' => $request->classroom_url]
        );

        return response()->json(['ok' => true]);
    }

    public function tambahMateri(Request $request)
    {
        $this->guardAdminOnly($request);
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'jenjang', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::create($data);
        return response()->json(['ok' => true]);
    }

    public function editMateri(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'jenjang', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::findOrFail($id)->update($data);
        return response()->json(['ok' => true]);
    }

    public function hapusMateri(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        Materi::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function tambahSekolah(Request $request)
    {
        $this->guardAdminOnly($request);
        Sekolah::create($request->only(['nama', 'jenjang', 'kota_kabupaten']));
        return response()->json(['ok' => true]);
    }

    public function editSekolah(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        Sekolah::findOrFail($id)->update($request->only(['nama', 'jenjang', 'kota_kabupaten']));
        return response()->json(['ok' => true]);
    }

    public function hapusSekolah(Request $request, int $id)
    {
        $this->guardAdminOnly($request);

        $sekolah = Sekolah::findOrFail($id);
        $jumlahKelasTerisi = $sekolah->kelas()
            ->whereNotNull('classroom_url')->where('classroom_url', '!=', '')
            ->count();

        if ($jumlahKelasTerisi > 0) {
            return response()->json([
                'detail' => "Tidak bisa dihapus, sekolah ini masih memiliki {$jumlahKelasTerisi} kelas Classroom yang terisi. Kosongkan dulu semua link Classroom-nya sebelum menghapus sekolah.",
            ], 400);
        }

        $sekolah->delete();
        return response()->json(['ok' => true]);
    }

    public function tambahMapel(Request $request)
    {
        $this->guardAdminOnly($request);
        try {
            MataPelajaran::create($request->only(['nama', 'jenjang']));
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Mata pelajaran sudah ada untuk jenjang ini.'], 400);
        }
        return response()->json(['ok' => true]);
    }

    public function editMapel(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        MataPelajaran::findOrFail($id)->update($request->only(['nama', 'jenjang']));
        return response()->json(['ok' => true]);
    }

    public function hapusMapel(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        $cnt = Materi::where('mapel_id', $id)->count();
        if ($cnt > 0) {
            return response()->json(['detail' => "Tidak bisa dihapus, masih ada {$cnt} materi yang menggunakan mata pelajaran ini."], 400);
        }
        MataPelajaran::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function updateProfile(Request $request)
    {
        $session = $request->admin_session;
        $admin = Admin::findOrFail($session->admin_id);

        if ($request->password_baru) {
            if (!$request->password_lama) {
                return response()->json(['detail' => 'Password lama wajib diisi.'], 400);
            }
            if (!password_verify($request->password_lama, $admin->password)) {
                return response()->json(['detail' => 'Password lama salah.'], 400);
            }
            if (strlen($request->password_baru) < 6) {
                return response()->json(['detail' => 'Password baru minimal 6 karakter.'], 400);
            }
            $admin->update(['nama' => $request->nama, 'password' => Hash::make($request->password_baru)]);
        } else {
            $admin->update(['nama' => $request->nama]);
        }

        return response()->json(['ok' => true]);
    }
}