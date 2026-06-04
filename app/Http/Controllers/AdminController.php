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
        ], $extra);
    }

    public function dashboard(Request $request)
    {
        return view('admin.dashboard', $this->adminCtx($request, ['active_menu' => 'dashboard']));
    }

    public function materi(Request $request)
    {
        return view('admin.materi', $this->adminCtx($request, ['active_menu' => 'materi']));
    }

    public function materiTambah(Request $request)
    {
        return view('admin.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => null]));
    }

    public function materiEdit(Request $request, int $id)
    {
        $materi = Materi::with('mapel')->findOrFail($id);
        return view('admin.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => $materi]));
    }

    public function classroom(Request $request)
    {
        return view('admin.classroom', $this->adminCtx($request, ['active_menu' => 'classroom']));
    }

    public function mapel(Request $request)
    {
        return view('admin.mapel', $this->adminCtx($request, ['active_menu' => 'mapel']));
    }

    public function users(Request $request)
    {
        return view('admin.admin_users', $this->adminCtx($request, ['active_menu' => 'users']));
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