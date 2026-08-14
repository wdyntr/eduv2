<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleApiController extends Controller
{
    /**
     * CRUD ROLE hanya untuk pengguna yang memiliki
     * permission sistem.kelola.
     */
    private function guardRoleManagement(Request $request): void
    {
        abort_if(
            !$request->user()?->can('sistem.kelola'),
            403,
            'Anda tidak memiliki izin untuk mengelola role.'
        );
    }

    /**
     * Daftar role yang boleh ditampilkan pada dropdown
     * Tambah User.
     */
    public function options(Request $request)
    {
        abort_if(
            !$request->user()?->can('users.kelola'),
            403,
            'Anda tidak memiliki izin untuk melihat daftar role.'
        );

        $currentPermissions = $request->user()
            ->getAllPermissions()
            ->pluck('name');

        $roles = Role::where('guard_name', 'web')
            ->with('permissions:name')
            ->orderBy('name')
            ->get()
            ->filter(function (Role $role) use ($currentPermissions) {
                $rolePermissions = $role->permissions->pluck('name');

                return $rolePermissions
                    ->diff($currentPermissions)
                    ->isEmpty();
            })
            ->map(function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'label' => str($role->name)
                        ->replace(['_', '-'], ' ')
                        ->headline(),
                    'requires_sekolah' => (bool) $role->requires_sekolah,
                ];
            })
            ->values();

        return response()->json([
            'items' => $roles,
        ]);
    }

    /**
     * Daftar role lengkap untuk halaman Kelola Role.
     */
    public function index(Request $request)
    {
        $this->guardRoleManagement($request);

        $roles = Role::where('guard_name', 'web')
            ->with('permissions:id,name')
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'label' => str($role->name)
                        ->replace(['_', '-'], ' ')
                        ->headline(),
                    'requires_sekolah' => (bool) $role->requires_sekolah,
                    'permissions' => $role->permissions
                        ->pluck('name')
                        ->values()
                        ->all(),
                    'jumlah_user' => $role->users_count,
                ];
            });

        return response()->json([
            'items' => $roles,
        ]);
    }

    /**
     * Daftar permission yang dapat diberikan ke role.
     */
    public function permissions(Request $request)
    {
        $this->guardRoleManagement($request);

        return response()->json([
            'items' => Permission::where('guard_name', 'web')
                ->orderBy('name')
                ->pluck('name')
                ->values()
                ->all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->guardRoleManagement($request);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('roles', 'name'),
            ],
            'permissions' => ['array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web')),
            ],
            'requires_sekolah' => ['boolean'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
        $role->requires_sekolah = $data['requires_sekolah'] ?? false;
        $role->save();

        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json([
            'ok' => true,
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
            ],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->guardRoleManagement($request);

        $role = Role::where('guard_name', 'web')
            ->findOrFail($id);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web')),
            ],
            'requires_sekolah' => ['boolean'],
        ]);

        $role->name = $data['name'];
        $role->requires_sekolah = $data['requires_sekolah'] ?? false;
        $role->save();

        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->guardRoleManagement($request);

        $role = Role::where('guard_name', 'web')
            ->findOrFail($id);

        if ($role->users()->exists()) {
            return response()->json([
                'detail' => 'Tidak bisa dihapus, masih ada akun yang memakai role ini.',
            ], 400);
        }

        $role->delete();

        return response()->json([
            'ok' => true,
        ]);
    }
}