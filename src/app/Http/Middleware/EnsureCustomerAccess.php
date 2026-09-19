<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAccess
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        // Staff and Admin are not allowed in the customer account area
        if ($user->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        // Deactivated accounts are logged out and blocked
        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['identifier' => 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ bộ phận hỗ trợ.']);
        }

        return $next($request);
    }
}
