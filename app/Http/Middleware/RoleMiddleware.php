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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ✅ Cek is_active DULU sebelum cek role
        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi admin.']);
        }

        if (auth()->user()->role !== $role) {
            if (auth()->user()->role === 'siswa') {
                return redirect()->route('quiz.index');
            }
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}