<?php
namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\UserSession;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function adminCtx(Request $request, array $extra = []): array
    {
        $user = $request->auth_user;

        return array_merge([
            'session_user'        => $user?->username ?? 'User',
            'session_id'          => $user?->id ?? 0,
            'session_role'        => $user?->getRoleNames()->first() ?? 'admin',
            'session_sekolah_id'  => $request->user_sekolah_id ?? null,
        ], $extra);
    }

    private function guardAdminOnly(Request $request)
    {
        abort_unless($request->auth_user?->hasRole('admin'), 403, 'Halaman ini hanya untuk admin.');
    }

    private function guardClassroomAccess(Request $request)
    {
        abort_unless($request->auth_user?->hasAnyRole(['admin', 'sekolah']), 403, 'Halaman ini tidak tersedia untuk peran Anda.');
    }

    private function guardJurnalAccess(Request $request)
    {
        abort_unless($request->auth_user?->hasAnyRole(['admin', 'guru']), 403, 'Halaman ini tidak tersedia untuk peran Anda.');
    }

    public function dashboard(Request $request)
    {
        $user = $request->auth_user;

        if ($user?->hasRole('guru')) {
            return view('admin.dashboard_penulis', $this->adminCtx($request, ['active_menu' => 'dashboard']));
        }
        if ($user?->hasRole('sekolah')) {
            return view('admin.dashboard_sekolah', $this->adminCtx($request, ['active_menu' => 'dashboard']));
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
        $this->guardClassroomAccess($request);
        return view('admin.classroom', $this->adminCtx($request, ['active_menu' => 'classroom']));
    }

    public function classroomDetail(Request $request, int $id)
    {
        $this->guardClassroomAccess($request);

        $user = $request->auth_user;
        if ($user?->hasRole('sekolah') && (int) ($request->user_sekolah_id ?? 0) !== $id) {
            abort(403, 'Anda tidak memiliki akses ke data sekolah ini.');
        }

        return view('admin.sekolah_kelas', $this->adminCtx($request, ['active_menu' => 'classroom', 'sekolah_id' => $id]));
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
        $this->guardJurnalAccess($request);
        return view('admin.jurnal', $this->adminCtx($request, ['active_menu' => 'jurnal']));
    }

    public function profile(Request $request)
    {
        return view('admin.profile', $this->adminCtx($request, ['active_menu' => 'profile']));
    }

    public function logout(Request $request)
    {
        $token = $request->cookie('user_session');
        if ($token) {
            UserSession::where('token', $token)->delete();
        }
        return redirect('/')->withCookie(cookie()->forget('user_session'));
    }
}