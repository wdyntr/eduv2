<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminSession;
use App\Models\Materi;
use App\Models\Sekolah;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
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
        $items = Admin::select('id', 'username', 'nama', 'created_at')->orderBy('id')->get();
        return response()->json(['items' => $items, 'total' => $items->count()]);
    }

    public function tambahAdmin(Request $request)
    {
        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama ?? '',
        ]);
        return response()->json(['ok' => true]);
    }

    public function hapusAdmin(Request $request, int $id)
    {
        $session = $request->admin_session;
        if ($session->admin_id == $id) {
            return response()->json(['detail' => 'Tidak bisa hapus akun sendiri.'], 400);
        }
        Admin::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function tambahMateri(Request $request)
    {
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'jenjang', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::create($data);
        return response()->json(['ok' => true]);
    }

    public function editMateri(Request $request, int $id)
    {
        $data = $request->only(['judul', 'deskripsi', 'tipe', 'jenjang', 'mapel_id', 'url']);
        $data['thumbnail'] = Materi::resolveThumbnail($data['tipe'] ?? '', $data['url'] ?? null);

        Materi::findOrFail($id)->update($data);
        return response()->json(['ok' => true]);
    }

    public function hapusMateri(int $id)
    {
        Materi::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function tambahSekolah(Request $request)
    {
        Sekolah::create($request->only(['nama', 'jenjang', 'kota_kabupaten', 'classroom_url']));
        return response()->json(['ok' => true]);
    }

    public function editSekolah(Request $request, int $id)
    {
        Sekolah::findOrFail($id)->update($request->only(['nama', 'jenjang', 'kota_kabupaten', 'classroom_url']));
        return response()->json(['ok' => true]);
    }

    public function hapusSekolah(int $id)
    {
        Sekolah::destroy($id);
        return response()->json(['ok' => true]);
    }

    public function tambahMapel(Request $request)
    {
        try {
            MataPelajaran::create($request->only(['nama', 'jenjang']));
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Mata pelajaran sudah ada untuk jenjang ini.'], 400);
        }
        return response()->json(['ok' => true]);
    }

    public function editMapel(Request $request, int $id)
    {
        MataPelajaran::findOrFail($id)->update($request->only(['nama', 'jenjang']));
        return response()->json(['ok' => true]);
    }

    public function hapusMapel(int $id)
    {
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
