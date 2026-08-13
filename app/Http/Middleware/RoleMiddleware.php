<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pasang setelah AdminAuth di route/group, mis:
 *   Route::middleware(['admin.auth', 'role:admin'])->group(...)
 *
 * Catatan: middleware ini sebelumnya pakai auth()->user()->role dan
 * ->is_active, tapi kolom itu tidak ada di skema `users` yang sekarang
 * (role dikelola lewat tabel Spatie permission, dan tidak ada kolom
 * is_active di users). Jadi dirombak untuk pakai $request->auth_user
 * (di-set oleh AdminAuth) + Spatie hasRole().
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->auth_user;

        if (!$user) {
            return redirect('/')->withCookie(cookie()->forget('user_session'));
        }

        if (!$user->hasRole($role)) {
            // Sebelumnya ada redirect khusus untuk role 'siswa' ke quiz.index,
            // tapi role itu tidak ada di alur admin/guru/sekolah aplikasi ini,
            // jadi fallback digeneralisasi ke dashboard admin.
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}