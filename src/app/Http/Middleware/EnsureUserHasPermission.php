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
            abort(403, 'Ban can dang nhap de truy cap chuc nang nay.');
        }

        if (! $user->hasPermission($permission)) {
            abort(403, 'Ban khong du quyen thuc hien thao tac nay.');
        }

        return $next($request);
    }
}
