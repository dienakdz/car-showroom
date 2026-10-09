<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'Bạn cần đăng nhập để truy cập chức năng này.');
        }

        if (! $user->hasPermission($permission)) {
            abort(403, 'Bạn không đủ quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
