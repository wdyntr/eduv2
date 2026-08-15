<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Materi;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function adminCtx(Request $request, array $extra = []): array
    {
        $user = $request->user();

        return array_merge([
            'session_user' => $user?->username ?? 'User',
            'session_id' => $user?->id ?? 0,
            'session_role' => $user?->getRoleNames()->first() ?? '',
            'session_sekolah_id' => $user?->sekolah_id,
        ], $extra);
    }

    private function guardClassroomPageAccess(Request $request): void
    {
        abort_unless(
            $request->user()?->can('classroom.kelola')
            || $request->user()?->can('sistem.kelola'),
            403,
            'Halaman ini tidak tersedia untuk peran Anda.'
        );
    }

    /**
     * Semua guard di bawah ini pakai cek PERMISSION, bukan nama role.
     * Ini yang bikin role baru otomatis dapat akses tanpa ubah kode —
     * cukup kasih permission yang sesuai pas bikin role-nya.
     */
    private function guardKontenAccess(Request $request)
    {
        abort_unless($request->user()?->can('materi.kelola'), 403, 'Halaman ini tidak tersedia untuk peran Anda.');
    }

    private function guardUserManagementAccess(Request $request)
    {
        abort_unless($request->user()?->can('users.kelola'), 403, 'Halaman ini hanya untuk admin sistem.');
    }

    private function guardClassroomAccess(Request $request)
    {
        abort_unless(
            $request->user()?->can('classroom.kelola')
            || $request->user()?->can('sistem.kelola'),
            403,
            'Halaman ini tidak tersedia untuk peran Anda.'
        );
    }

    /** admin_sistem cuma punya jurnal.lihat (view-only), penulis punya jurnal.ajukan, reviewer_jurnal punya jurnal.review */
    private function guardJurnalAccess(Request $request)
    {
        $user = $request->user();
        abort_unless(
            $user?->can('jurnal.review') || $user?->can('jurnal.ajukan') || $user?->can('jurnal.lihat'),
            403,
            'Halaman ini tidak tersedia untuk peran Anda.'
        );
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $bisaAdmin    = (bool) $user?->can('sistem.kelola');
        $bisaReview   = (bool) $user?->can('jurnal.review');
        $bisaAjukan   = (bool) $user?->can('jurnal.ajukan');
        $bisaOperator = (bool) ($user?->can('materi.kelola') || $user?->can('classroom.kelola'));
        $adaSekolah   = (bool) $user?->sekolah_id;

        // Semua kombinasi (admin_sistem, reviewer_jurnal, penulis, sekolah, operator_konten,
        // atau gabungan mana pun) dirakit dari section per-permission ke SATU view yang sama:
        // user.dashboard — supaya kombinasi role otomatis dapat kombinasi section yang sesuai.
        $sections = [
            'admin'    => $bisaAdmin,
            'reviewer' => $bisaReview,
            'penulis'  => $bisaAjukan && !$bisaReview && !$bisaAdmin,
            'sekolah'  => $adaSekolah && $bisaOperator && !$bisaAdmin,
            'operator' => $bisaOperator && !$bisaAdmin && !$adaSekolah,
        ];

        if (!in_array(true, $sections, true)) {
            $sections['admin'] = true; // fallback kalau tidak match section manapun
        }

        return view('user.dashboard', $this->adminCtx($request, [
            'active_menu' => 'dashboard',
            'sections' => $sections,
        ]));
    }

    public function materi(Request $request)
    {
        $this->guardKontenAccess($request);
        return view('user.materi', $this->adminCtx($request, ['active_menu' => 'materi']));
    }

    public function materiTambah(Request $request)
    {
        $this->guardKontenAccess($request);
        return view('user.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => null]));
    }

    public function materiEdit(Request $request, int $id)
    {
        $this->guardKontenAccess($request);
        $materi = Materi::with('mapel')->findOrFail($id);
        return view('user.materi_form', $this->adminCtx($request, ['active_menu' => 'materi', 'materi' => $materi]));
    }

    public function classroom(Request $request)
    {
        $this->guardClassroomPageAccess($request);

        return view(
            'user.classroom',
            $this->adminCtx($request, [
                'active_menu' => 'classroom',
            ])
        );
    }

    public function classroomDetail(Request $request, int $id)
    {
        $this->guardClassroomAccess($request);

        $user = $request->user();

        if ($user?->sekolah_id && (int) $user->sekolah_id !== $id) {
            abort(403, 'Anda tidak memiliki akses ke data sekolah ini.');
        }

        return view(
            'user.sekolah_kelas',
            $this->adminCtx($request, [
                'active_menu' => 'classroom',
                'sekolah_id' => $id
            ])
        );
    }

    public function mapel(Request $request)
    {
        $this->guardKontenAccess($request);
        return view('user.mapel', $this->adminCtx($request, ['active_menu' => 'mapel']));
    }

    public function users(Request $request)
    {
        $this->guardUserManagementAccess($request);
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

    public function roles(Request $request)
    {
        abort_unless(
            $request->user()?->can('sistem.kelola'),
            403,
            'Halaman ini hanya dapat diakses oleh pengguna yang memiliki izin sistem.'
        );

        return view(
            'user.roles',
            $this->adminCtx($request, [
                'active_menu' => 'roles'
            ])
        );
    }
}