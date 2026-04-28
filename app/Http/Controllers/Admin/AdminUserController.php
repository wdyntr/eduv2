<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// app/Http/Controllers/Admin/AdminUserController.php
class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('username', 'like', "%$s%")
                  ->orWhere('no_induk', 'like', "%$s%")
            )
            ->when($request->role, fn($q, $r) => $q->where('role', $r))
            ->when($request->kelas, fn($q, $k) => $q->where('kelas', $k))
            ->latest()->paginate(20)->withQueryString();

        $kelasList = User::where('role', 'siswa')
                         ->whereNotNull('kelas')
                         ->distinct()->pluck('kelas')->sort();

        return view('admin.users.index', compact('users', 'kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|unique:users|max:255',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:admin,siswa',
            'kelas'     => 'nullable|required_if:role,siswa|string|max:255',
            'no_induk'  => 'nullable|unique:users|max:255',
            'is_active' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => "required|string|unique:users,username,{$user->id}|max:255",
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:admin,siswa',
            'kelas'    => 'nullable|string|max:255',
            'no_induk' => "nullable|unique:users,no_induk,{$user->id}|max:255",
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return back()->with('success', 'Data user diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status user diperbarui.');
    }
}
