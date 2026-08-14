<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Materi;
use App\Models\Sekolah;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminApiController extends Controller
{
    private function guardAdminOnly(Request $request): void
    {
        abort_if(!$request->user()?->can('users.kelola'), 403, 'Hanya admin sistem yang bisa melakukan aksi ini.');
    }

    /** Materi & Mata Pelajaran: siapa pun dengan permission materi.kelola */
    private function guardKontenAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('materi.kelola'), 403, 'Anda tidak memiliki akses untuk mengelola konten.');
    }

    /** Kelola data sekolah (bukan link classroom-nya, itu beda permission) */
    private function guardSistemAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('sistem.kelola'), 403, 'Anda tidak memiliki akses untuk mengelola data ini.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['detail' => 'Username atau password salah.'], 401);
        }

        $request->session()->regenerate();

        return response()->json(['ok' => true]);
    }

    public function getUsers(Request $request)
    {
        $this->guardAdminOnly($request);
        $items = User::with(['sekolah:id,nama', 'roles:name'])
            ->select('id', 'username', 'nama', 'sekolah_id', 'created_at')
            ->orderBy('id')->get()
            ->map(function ($u) {
                $u->role = $u->roles->pluck('name')->first();
                unset($u->roles);
                return $u;
            });
        return response()->json(['items' => $items, 'total' => $items->count()]);
    }

    /** Daftar role yang tersedia untuk dipilih — dinamis dari tabel roles, bukan hardcode */
    public function getRoles(Request $request)
    {
        $this->guardAdminOnly($request);

        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get(['name'])
            ->map(fn ($role) => [
                'name' => $role->name,
                'label' => str($role->name)->replace(['_', '-'], ' ')->headline(),
                'requires_sekolah' => $role->name === 'sekolah',
            ]);

        return response()->json(['items' => $roles]);
    }

    public function tambahAdmin(Request $request)
    {
        $this->guardAdminOnly($request);

        $data = $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:6|max:255',
            'nama' => 'nullable|string|max:150',
            'role' => 'required|string|exists:roles,name',
            'sekolah_id' => 'nullable|integer|exists:sekolah,id',
        ]);

        // Hanya role "sekolah" yang secara UI/API ini di-scope ke sekolah tertentu.
        // Role lain tetap boleh dikaitkan ke sekolah jika memang dikirim, tetapi
        // kita cegah akun sekolah dibuat tanpa sekolah_id.
        if ($data['role'] === 'sekolah' && empty($data['sekolah_id'])) {
            return response()->json(['detail' => 'Role sekolah wajib memiliki sekolah.'], 422);
        }

        $user = User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'nama' => $data['nama'] ?? '',
            'sekolah_id' => $data['sekolah_id'] ?? null,
        ]);
        $user->assignRole($data['role']);

        return response()->json(['ok' => true]);
    }

    public function hapusAdmin(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        if ($request->user()->id == $id) {
            return response()->json(['detail' => 'Tidak bisa hapus akun sendiri.'], 400);
        }
        User::destroy($id);
        return response()->json(['ok' => true]);
    }

    // =====================
    // MONITORING & KELOLA LINK CLASSROOM PER MATA PELAJARAN
    // =====================

    private function guardClassroomAccess(Request $request): void
    {
        abort_if(!$request->user()?->can('classroom.kelola'), 403, 'Tidak memiliki akses.');
    }

    /** Akun yang sekolah_id-nya diisi cuma boleh akses sekolahnya sendiri; yang kosong (global) boleh akses semua */
    private function assertSekolahAccess(Request $request, int $sekolahId): void
    {
        $user = $request->user();
        if (!$user?->sekolah_id) return;
        abort_if((int) $user->sekolah_id !== $sekolahId, 403, 'Anda tidak memiliki akses ke data sekolah ini.');
    }

    public function sekolahKelasList(Request $request, int $id)
    {
        $this->guardClassroomAccess($request);
        $this->assertSekolahAccess($request, $id);

        $bulan = now()->format('Y-m');
        $sekolah = Sekolah::with(['jenjang:id,kode,nama', 'kotaKabupaten:id,nama'])->findOrFail($id);
        $mapel = MataPelajaran::where('jenjang_id', $sekolah->jenjang_id)->orderBy('nama')->get(['id', 'nama']);
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

    public function sekolahKelasUpdate(Request $request, int $id, int $mapelId)
    {
        $this->guardClassroomAccess($request);
        $this->assertSekolahAccess($request, $id);

        $sekolah = Sekolah::findOrFail($id);
        $mapel = MataPelajaran::where('jenjang_id', $sekolah->jenjang_id)->findOrFail($mapelId);

        $request->validate(['classroom_url' => 'nullable|url|max:500']);

        \App\Models\SekolahKelas::updateOrCreate(
            ['sekolah_id' => $sekolah->id, 'mapel_id' => $mapel->id],
            ['classroom_url' => $request->classroom_url]
        );

        return response()->json(['ok' => true]);
    }

    public function tambahMateri(Request $request)
    {
        $this->guardKontenAccess($request);
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::create($data);
        return response()->json(['ok' => true]);
    }

    public function editMateri(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::findOrFail($id)->update($data);
        return response()->json(['ok' => true]);
    }

    public function hapusMateri(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        Materi::destroy($id);
        return response()->json(['ok' => true]);
    }

    private function resolveSekolahData(Request $request): array
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'jenjang' => 'nullable|string|exists:jenjang,kode',
            'jenjang_id' => 'nullable|integer|exists:jenjang,id',
            'kota_kabupaten' => 'nullable|string|max:150',
            'kota_kabupaten_id' => 'nullable|integer|exists:kota_kabupaten,id',
        ]);

        if (empty($data['jenjang_id']) && !empty($data['jenjang'])) {
            $data['jenjang_id'] = \App\Models\Jenjang::where('kode', $data['jenjang'])->value('id');
        }

        if (empty($data['jenjang_id'])) {
            abort(422, 'Jenjang wajib dipilih.');
        }

        // Frontend lama mengirim nama kota; konversi ke FK bila tersedia.
        if (empty($data['kota_kabupaten_id']) && !empty($data['kota_kabupaten'])) {
            $data['kota_kabupaten_id'] = \App\Models\KotaKabupaten::whereRaw('LOWER(nama) = ?', [strtolower(trim($data['kota_kabupaten']))])->value('id');
        }

        return [
            'nama' => $data['nama'],
            'jenjang_id' => $data['jenjang_id'],
            'kota_kabupaten_id' => $data['kota_kabupaten_id'] ?? null,
        ];
    }

    public function tambahSekolah(Request $request)
    {
        $this->guardSistemAccess($request);
        Sekolah::create($this->resolveSekolahData($request));
        return response()->json(['ok' => true]);
    }

    public function editSekolah(Request $request, int $id)
    {
        $this->guardSistemAccess($request);
        Sekolah::findOrFail($id)->update($this->resolveSekolahData($request));
        return response()->json(['ok' => true]);
    }

    public function hapusSekolah(Request $request, int $id)
    {
        $this->guardSistemAccess($request);

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
        $this->guardKontenAccess($request);
        try {
            MataPelajaran::create($request->only(['nama', 'jenjang_id']));
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Mata pelajaran sudah ada untuk jenjang ini.'], 400);
        }
        return response()->json(['ok' => true]);
    }

    public function editMapel(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        MataPelajaran::findOrFail($id)->update($request->only(['nama', 'jenjang_id']));
        return response()->json(['ok' => true]);
    }

    public function hapusMapel(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        $cnt = Materi::where('mapel_id', $id)->count();
        if ($cnt > 0) {
            return response()->json(['detail' => "Tidak bisa dihapus, masih ada {$cnt} materi yang menggunakan mata pelajaran ini."], 400);
        }
        MataPelajaran::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($request->password_baru) {
            if (!$request->password_lama) {
                return response()->json(['detail' => 'Password lama wajib diisi.'], 400);
            }
            if (!password_verify($request->password_lama, $user->password)) {
                return response()->json(['detail' => 'Password lama salah.'], 400);
            }
            if (strlen($request->password_baru) < 6) {
                return response()->json(['detail' => 'Password baru minimal 6 karakter.'], 400);
            }
            $user->update(['nama' => $request->nama, 'password' => Hash::make($request->password_baru)]);
        } else {
            $user->update(['nama' => $request->nama]);
        }

        return response()->json(['ok' => true]);
    }
}