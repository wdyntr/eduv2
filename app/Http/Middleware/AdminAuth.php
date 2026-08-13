<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSession;
use App\Models\User;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('user_session');

        if (!$token) {
            return $this->unauthorized($request);
        }

        $session = UserSession::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return $this->unauthorized($request);
        }

        $user = User::find($session->user_id);

        if (!$user) {
            return $this->unauthorized($request);
        }

        // auth_user membawa model User lengkap supaya controller/middleware
        // lain bisa cek role/permission langsung lewat Spatie: hasRole(), can(), dst.
        $request->merge([
            'user_session'    => $session,
            'auth_user'       => $user,
            'user_sekolah_id' => $user->sekolah_id,
        ]);

        return $next($request);
    }

    private function unauthorized(Request $request)
    {
        if ($request->is('api/*')) {
            return response()->json(['detail' => 'Session expired'], 401);
        }
        return response('<script>localStorage.setItem("openLogin","1");window.location.href="/";</script>');
    }
}