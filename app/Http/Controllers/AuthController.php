<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek apakah user aktif sebelum login
        $user = \App\Models\User::where('username', $credentials['username'])->first();

        // Debug sementara — hapus setelah masalah terselesaikan
        if (!$user) {
            return back()->withErrors(['username' => 'DEBUG: User tidak ditemukan di DB.']);
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('username'))
                        ->withErrors(['username' => 'Akun Anda tidak aktif.']);
        }

        if (!Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withInput($request->only('username'))
                        ->withErrors(['username' => 'DEBUG: attempt() gagal — password salah atau hash tidak cocok.']);
        }

        if ($user && !$user->is_active) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi admin.']);
        }

        if (!Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user()->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match($role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'siswa'  => redirect()->route('quiz.index'),
            default  => redirect()->route('login'),
        };
    }
}