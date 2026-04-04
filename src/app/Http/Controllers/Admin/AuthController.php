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
            abort(403, 'Tai khoan hien tai khong co quyen truy cap khu vuc quan tri.');
        }

        return $this->adminView('admin.auth.login', [
            'adminPageTitle' => 'Dang nhap quan tri',
            'adminPageDescription' => 'Truy cap dashboard de quan ly inventory, CRM va van hanh showroom.',
            'withoutAdminChrome' => true,
        ]);
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        [$field, $value] = $this->resolveLoginField($request->string('identifier')->value());

        if (! Auth::attempt([$field => $value, 'password' => $request->string('password')->value()], $request->boolean('remember'))) {
            return back()
                ->withErrors(['identifier' => 'Thong tin dang nhap khong hop le.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        if (! $request->user()?->hasAnyRole(['admin', 'staff'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['identifier' => 'Tai khoan nay khong duoc cap quyen quan tri.'])
                ->withInput($request->except('password'));
        }

        $this->pushSuccessToast('Dang nhap khu vuc quan tri thanh cong.');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $this->pushSuccessToast('Da dang xuat khoi dashboard.');
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
