<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Belum login → ke halaman login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Sudah login tapi role salah
        if (auth()->user()->role !== $role) {
            // Siswa coba akses admin → balik ke quiz
            if (auth()->user()->role === 'siswa') {
                return redirect()->route('quiz.index');
            }
            // Admin coba akses quiz siswa → balik ke dashboard
            return redirect()->route('admin.dashboard');
        }

        // Akun nonaktif
        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda tidak aktif.']);
        }

        return $next($request);
    }
}