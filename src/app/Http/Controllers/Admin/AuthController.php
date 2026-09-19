<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends AdminBaseController
{
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user !== null && $user->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user !== null) {
            abort(403, 'Tài khoản hiện tại không có quyền truy cập khu vực quản trị.');
        }

        return $this->adminView('admin.auth.login', [
            'adminPageTitle' => 'Đăng nhập quản trị',
            'adminPageDescription' => 'Truy cập bảng điều khiển để quản lý kho xe, CRM và vận hành showroom.',
            'withoutAdminChrome' => true,
        ]);
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        [$field, $value] = $this->resolveLoginField($request->string('identifier')->value());

        if (! Auth::attempt([$field => $value, 'password' => $request->string('password')->value()], $request->boolean('remember'))) {
            return back()
                ->withErrors(['identifier' => 'Thông tin đăng nhập không hợp lệ.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        if (! $request->user()?->hasAnyRole(['admin', 'staff'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['identifier' => 'Tài khoản này không được cấp quyền quản trị.'])
                ->withInput($request->except('password'));
        }

        if (! (bool) $request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['identifier' => 'Tài khoản quản trị của bạn đã bị tạm khóa. Vui lòng liên hệ quản trị viên.'])
                ->withInput($request->except('password'));
        }

        $this->pushSuccessToast('Đăng nhập khu vực quản trị thành công.');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $this->pushSuccessToast('Đã đăng xuất khỏi hệ thống quản trị.');
        }

        return redirect()->route('admin.login');
    }

    protected function resolveLoginField(string $identifier): array
    {
        $identifier = trim($identifier);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return ['email', strtolower($identifier)];
        }

        $normalizedPhone = preg_replace('/\D+/', '', $identifier) ?? '';
        if ($normalizedPhone !== '') {
            return ['phone', $normalizedPhone];
        }

        return ['name', $identifier];
    }
}
