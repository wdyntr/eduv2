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
        \Log::info('ADMIN AUTH - RAW COOKIE', [
            'cookie_header' => $request->header('Cookie'),
            'all_cookies' => $request->cookies->all(),
        ]);

        $token = $request->cookie('user_session');

        \Log::info('ADMIN AUTH - TOKEN', [
            'has_token' => !empty($token),
        ]);

        if (!$token) {
            \Log::info('ADMIN AUTH - NO TOKEN');
            return $this->unauthorized($request);
        }

        $session = UserSession::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        \Log::info('ADMIN AUTH - SESSION', [
            'found' => (bool) $session,
            'user_id' => $session?->user_id,
            'expires_at' => $session?->expires_at,
        ]);

        if (!$session) {
            return $this->unauthorized($request);
        }

        $user = User::find($session->user_id);

        \Log::info('ADMIN AUTH - USER', [
            'found' => (bool) $user,
            'user_id' => $user?->id,
            'username' => $user?->username,
        ]);

        if (!$user) {
            return $this->unauthorized($request);
        }

        $request->merge([
            'user_session'    => $session,
            'auth_user'       => $user,
            'user_sekolah_id' => $user->sekolah_id,
        ]);

        \Log::info('ADMIN AUTH - PASSED');

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