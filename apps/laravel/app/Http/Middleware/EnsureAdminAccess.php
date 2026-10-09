<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('admin.login');
        }

        if (! $user->hasAnyRole(['admin', 'staff'])) {
            abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['identifier' => 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ quản trị viên.']);
        }

        return $next($request);
    }
}
