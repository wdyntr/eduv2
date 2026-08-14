<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserApiController extends Controller
{
    private function guardUserManagement(Request $request): void
    {
        abort_if(
            !$request->user()?->can('users.kelola'),
            403,
            'Anda tidak memiliki izin untuk mengelola pengguna.'
        );
    }

    public function index(Request $request)
    {
        $this->guardUserManagement($request);

        $items = User::with([
            'sekolah:id,nama',
            'roles:name'
        ])
            ->select(
                'id',
                'username',
                'nama',
                'sekolah_id',
                'created_at'
            )
            ->orderBy('id')
            ->get()
            ->map(function ($u) {
                $u->role = $u->roles
                    ->pluck('name')
                    ->first();

                unset($u->roles);

                return $u;
            });

        return response()->json([
            'items' => $items,
            'total' => $items->count(),
        ]);
    }

    public function store(Request $request)
    {
        $this->guardUserManagement($request);

        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],
            'nama' => [
                'nullable',
                'string',
                'max:150',
            ],
            'role' => [
                'required',
                'string',
                'exists:roles,name',
            ],
            'sekolah_id' => [
                'nullable',
                'integer',
                'exists:sekolah,id',
            ],
        ]);

        $role = Role::where('guard_name', 'web')
            ->where('name', $data['role'])
            ->with('permissions')
            ->firstOrFail();

        /*
         * User hanya boleh memberikan role yang permission-nya
         * seluruhnya sudah dimiliki oleh dirinya sendiri.
         *
         * Contoh:
         * users.kelola saja
         * tidak boleh memberikan admin_sistem.
         */
        $currentPermissions = $request->user()
            ->getAllPermissions()
            ->pluck('name');

        $targetPermissions = $role
            ->permissions
            ->pluck('name');

        if ($targetPermissions->diff($currentPermissions)->isNotEmpty()) {
            abort(
                403,
                'Anda tidak memiliki izin untuk memberikan role tersebut.'
            );
        }

        if (
            $role->requires_sekolah
            && empty($data['sekolah_id'])
        ) {
            return response()->json([
                'detail' => 'Role ini wajib memiliki sekolah.'
            ], 422);
        }

        $user = User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'nama' => $data['nama'] ?? '',
            'sekolah_id' => $data['sekolah_id'] ?? null,
        ]);

        $user->assignRole($role);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->guardUserManagement($request);

        if ((int) $request->user()->id === $id) {
            return response()->json([
                'detail' => 'Tidak bisa menghapus akun sendiri.'
            ], 400);
        }

        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'ok' => true,
        ]);
    }
}