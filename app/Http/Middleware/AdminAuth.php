<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AdminSession;
use App\Models\Admin;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('admin_session');

        if (!$token) {
            return $this->unauthorized($request);
        }

        $session = AdminSession::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return $this->unauthorized($request);
        }

        $admin = Admin::where('id', $session->admin_id)->first();
        $request->merge([
            'admin_session' => $session,
            'admin_role' => $admin->role ?? 'admin',
            'admin_sekolah_id' => $admin->sekolah_id ?? null,
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