<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleApiController extends Controller
{
    private function guard(Request $request): void
    {
        abort_if(!$request->user()?->can('users.kelola'), 403, 'Hanya admin sistem yang bisa mengelola role.');
    }

    public function index(Request $request)
    {
        $this->guard($request);

        $roles = Role::where('guard_name', 'web')
            ->with('permissions:id,name')
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'label' => str($r->name)->replace('_', ' ')->headline(),
                'requires_sekolah' => $r->name === 'sekolah',
                'permissions' => $r->permissions->pluck('name'),
                'jumlah_user' => $r->users_count,
            ]);

        return response()->json(['items' => $roles]);
    }

    /** Daftar permission yang tersedia untuk dicentang — fixed, dari kode (bukan dari UI ini) */
    public function permissions(Request $request)
    {
        $this->guard($request);
        return response()->json(['items' => Permission::where('guard_name', 'web')->orderBy('name')->pluck('name')]);
    }

    public function store(Request $request)
    {
        $this->guard($request);
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->guard($request);
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->guard($request);
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return response()->json(['detail' => 'Tidak bisa dihapus, masih ada akun yang memakai role ini.'], 400);
        }

        $role->delete();
        return response()->json(['ok' => true]);
    }
}