<?php
namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\AdminSession;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function adminCtx(Request $request, array $extra = []): array
    {
        $token = $request->cookie('admin_session');
        $session = $token ? AdminSession::where('token', $token)->first() : null;

        return array_merge([
            'session_user' => $session?->username ?? 'Admin',
            'session_id' => $session?->admin_id ?? 0,
            'session_role' => $request->admin_role ?? 'admin',
        ], $extra);
    }

    private function guardAdminOnly(Request $request)
    {
        if (($request->admin_role ?? 'admin') !== 'admin') {
            abort(403, 'Halaman ini hanya untuk admin.');
        }
    }

    public function dashboard(Request $request)
    {
        if (($request->admin_role ?? 'admin') === 'penulis') {
            return view('admin.dashboard_penulis', $this->adminCtx($request, ['active_menu' => 'dashboard']));
        }
        return view('admin.dashboard', $this->adminCtx($request, ['active_menu' => 'dashboard']));
    }

    public function materi(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('admin.materi', $this->adminCtx($request, ['active_menu' => 'materi']));
    }

    public function materiTambah(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('admin.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => null]));
    }

    public function materiEdit(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        $materi = Materi::with('mapel')->findOrFail($id);
        return view('admin.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => $materi]));
    }

    public function classroom(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('admin.classroom', $this->adminCtx($request, ['active_menu' => 'classroom']));
    }

    public function mapel(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('admin.mapel', $this->adminCtx($request, ['active_menu' => 'mapel']));
    }

    public function users(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('admin.admin_users', $this->adminCtx($request, ['active_menu' => 'users']));
    }

    public function jurnal(Request $request)
    {
        return view('admin.jurnal', $this->adminCtx($request, ['active_menu' => 'jurnal']));
    }

    public function profile(Request $request)
    {
        return view('admin.profile', $this->adminCtx($request, ['active_menu' => 'profile']));
    }

    public function logout(Request $request)
    {
        $token = $request->cookie('admin_session');
        if ($token) {
            \App\Models\AdminSession::where('token', $token)->delete();
        }
        return redirect('/')->withCookie(cookie()->forget('admin_session'));
    }
}
