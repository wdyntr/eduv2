<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use App\Models\UserSession;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function adminCtx(Request $request, array $extra = []): array
    {
        $user = $request->user();

        return array_merge([
            'session_user' => $user?->username ?? 'User',
            'session_id' => $user?->id ?? 0,
            'session_role' => $user?->getRoleNames()->first() ?? 'admin',
            'session_sekolah_id' => $user?->sekolah_id,
        ], $extra);
    }

    private function guardAdminOnly(Request $request)
    {
        abort_unless($request->user()?->hasRole('admin_sistem'), 403, 'Halaman ini hanya untuk admin.');
    }

    private function guardClassroomAccess(Request $request)
    {
        abort_unless($request->user()?->hasAnyRole(['admin_sistem', 'sekolah']), 403, 'Halaman ini tidak tersedia untuk peran Anda.');
    }

    private function guardJurnalAccess(Request $request)
    {
        abort_unless($request->user()?->hasAnyRole(['admin_sistem', 'guru']), 403, 'Halaman ini tidak tersedia untuk peran Anda.');
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if ($user?->hasRole('guru')) {
            return view('user.dashboard_penulis', $this->adminCtx($request, ['active_menu' => 'dashboard']));
        }
        if ($user?->hasRole('sekolah')) {
            return view('user.dashboard_sekolah', $this->adminCtx($request, ['active_menu' => 'dashboard']));
        }
        return view('user.dashboard', $this->adminCtx($request, ['active_menu' => 'dashboard']));
    }

    public function materi(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('user.materi', $this->adminCtx($request, ['active_menu' => 'materi']));
    }

    public function materiTambah(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('user.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => null]));
    }

    public function materiEdit(Request $request, int $id)
    {
        $this->guardAdminOnly($request);
        $materi = Materi::with('mapel')->findOrFail($id);
        return view('user.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => $materi]));
    }

    public function classroom(Request $request)
    {
        $this->guardClassroomAccess($request);
        return view('user.classroom', $this->adminCtx($request, ['active_menu' => 'classroom']));
    }

    public function classroomDetail(Request $request, int $id)
    {
        $this->guardClassroomAccess($request);

        $user = $request->user();
        if ($user?->hasRole('sekolah') && (int) ($request->user_sekolah_id ?? 0) !== $id) {
            abort(403, 'Anda tidak memiliki akses ke data sekolah ini.');
        }

        return view('user.sekolah_kelas', $this->adminCtx($request, ['active_menu' => 'classroom', 'sekolah_id' => $id]));
    }

    public function mapel(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('user.mapel', $this->adminCtx($request, ['active_menu' => 'mapel']));
    }

    public function users(Request $request)
    {
        $this->guardAdminOnly($request);
        return view('user.admin_users', $this->adminCtx($request, ['active_menu' => 'users']));
    }

    public function jurnal(Request $request)
    {
        $this->guardJurnalAccess($request);
        return view('user.jurnal', $this->adminCtx($request, ['active_menu' => 'jurnal']));
    }

    public function profile(Request $request)
    {
        return view('user.profile', $this->adminCtx($request, ['active_menu' => 'profile']));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}