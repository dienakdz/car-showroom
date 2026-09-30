<?php

namespace App\Http\Controllers\Clients;

use App\Mail\CustomerActivationMail;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AuthController extends ClientBaseController
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof User && $user->hasAnyRole(['admin', 'staff'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('account.show');
        }

        return $this->viewWithSharedData('client.auth');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof User && $user->hasAnyRole(['admin', 'staff'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('account.show');
        }

        $credentials = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'form_mode' => ['nullable', 'string'],
        ]);

        [$field, $value] = $this->resolveLoginField($credentials['identifier']);

        if (! Auth::attempt([$field => $value, 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()
                ->withErrors(['identifier' => 'Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại tài khoản hoặc mật khẩu.'])
                ->withInput($request->except('password'));
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            Auth::logout();

            return back()
                ->withErrors(['identifier' => 'Không thể xác thực người dùng.'])
                ->withInput($request->except('password'));
        }

        // Admin and Staff accounts cannot log in through client portal
        if ($user->hasAnyRole(['admin', 'staff'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['identifier' => 'Tài khoản nhân viên / quản trị không được đăng nhập tại cổng khách hàng. Vui lòng đăng nhập tại Trang quản trị.'])
                ->withInput($request->except('password'));
        }

        // Unverified accounts cannot log in
        if (! $user->hasVerifiedEmail()) {
            $unverifiedEmail = $user->email;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['identifier' => 'Tài khoản của bạn chưa được kích hoạt qua email. Vui lòng kiểm tra hộp thư đến (cả mục Spam) hoặc nhấn vào liên kết bên dưới để gửi lại email kích hoạt.'])
                ->with('unverified_email', $unverifiedEmail)
                ->withInput($request->except('password'));
        }

        // Inactive accounts cannot log in
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['identifier' => 'Tài khoản của bạn đã bị tạm khóa bởi quản trị viên. Vui lòng liên hệ bộ phận hỗ trợ.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $this->pushSuccessToast('Đăng nhập thành công.');

        return redirect()->intended(route('home'));
    }

    public function register(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('account.show');
        }

        $request->merge([
            'email' => $this->normalizeEmail($request->input('email')),
            'phone' => $this->normalizePhone($request->input('phone')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'accept_privacy' => ['accepted'],
            'form_mode' => ['nullable', 'string'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'email.required' => 'Vui lòng nhập địa chỉ email để nhận liên kết kích hoạt tài khoản.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng. Vui lòng đăng nhập hoặc sử dụng email khác.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'accept_privacy.accepted' => 'Bạn cần đồng ý với chính sách bảo mật để tạo tài khoản.',
        ]);

        $user = User::query()->create([
            'name' => trim((string) $data['name']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_active' => false,
            'email_verified_at' => null,
        ]);

        $this->attachCustomerRole($user);

        $activationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            [
                'id' => $user->id,
                'hash' => sha1((string) $user->email),
            ]
        );

        try {
            Mail::to($user->email)->send(new CustomerActivationMail($user, $activationUrl));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('login')
            ->with('auth_notice', [
                'type' => 'success',
                'title' => 'Đăng ký tài khoản thành công!',
                'message' => 'Hệ thống đã gửi liên kết kích hoạt tới email ' . $user->email . '. Quý khách vui lòng kiểm tra hộp thư đến (bao gồm cả thư rác / Spam) và nhấp vào liên kết để kích hoạt tài khoản trước khi đăng nhập.',
                'email' => $user->email,
            ]);
    }

    public function activate(Request $request, int|string $id, string $hash): RedirectResponse
    {
        $user = User::query()->find($id);

        if (! $user instanceof User) {
            return redirect()->route('login')
                ->with('auth_notice', [
                    'type' => 'danger',
                    'title' => 'Kích hoạt không thành công',
                    'message' => 'Tài khoản không tồn tại hoặc liên kết kích hoạt không hợp lệ.',
                ]);
        }

        if (! hash_equals((string) $hash, sha1((string) $user->email))) {
            return redirect()->route('login')
                ->with('auth_notice', [
                    'type' => 'danger',
                    'title' => 'Liên kết không hợp lệ',
                    'message' => 'Mã xác thực tài khoản không chính xác hoặc đã bị thay đổi.',
                ]);
        }

        if ($user->hasVerifiedEmail() && $user->is_active) {
            return redirect()->route('login')
                ->with('auth_notice', [
                    'type' => 'info',
                    'title' => 'Tài khoản đã kích hoạt',
                    'message' => 'Tài khoản của quý khách đã được kích hoạt trước đó. Quý khách có thể đăng nhập ngay.',
                ]);
        }

        $user->markEmailAsVerified();

        $this->pushSuccessToast('Kích hoạt tài khoản thành công!');

        return redirect()->route('login')
            ->with('auth_notice', [
                'type' => 'success',
                'title' => 'Kích hoạt tài khoản thành công!',
                'message' => 'Chúc mừng ' . $user->name . '! Tài khoản của quý khách đã được kích hoạt thành công. Hãy đăng nhập để trải nghiệm đầy đủ tính năng.',
                'email' => $user->email,
            ]);
    }

    public function resendActivation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email cần kích hoạt.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $normalizedEmail = $this->normalizeEmail($validated['email']);
        $user = $normalizedEmail !== null ? User::query()->where('email', $normalizedEmail)->first() : null;

        if (! $user instanceof User) {
            return back()->with('auth_notice', [
                'type' => 'info',
                'title' => 'Thông báo gửi email',
                'message' => 'Nếu địa chỉ email tồn tại trong hệ thống và chưa kích hoạt, liên kết mới đã được gửi.',
            ]);
        }

        if ($user->hasVerifiedEmail() && $user->is_active) {
            return back()->with('auth_notice', [
                'type' => 'info',
                'title' => 'Tài khoản đã kích hoạt',
                'message' => 'Tài khoản này đã được kích hoạt trước đó. Quý khách có thể đăng nhập ngay.',
            ]);
        }

        $activationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            [
                'id' => $user->id,
                'hash' => sha1((string) $user->email),
            ]
        );

        try {
            Mail::to($user->email)->send(new CustomerActivationMail($user, $activationUrl));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('auth_notice', [
            'type' => 'success',
            'title' => 'Đã gửi lại email kích hoạt',
            'message' => 'Hệ thống đã gửi lại liên kết kích hoạt mới tới email ' . $user->email . '. Quý khách vui lòng kiểm tra hộp thư đến (bao gồm cả mục Spam).',
            'email' => $user->email,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $this->pushSuccessToast('Đã đăng xuất.');
        }

        return redirect()->route('home');
    }

    protected function resolveLoginField(string $identifier): array
    {
        $identifier = trim($identifier);

        $email = $this->normalizeEmail($identifier);
        if ($email !== null) {
            return ['email', $email];
        }

        $phone = $this->normalizePhone($identifier);
        if ($phone !== null) {
            return ['phone', $phone];
        }

        return ['name', $identifier];
    }

    protected function normalizeEmail(mixed $email): ?string
    {
        $email = is_string($email) ? strtolower(trim($email)) : '';

        return $email !== '' ? $email : null;
    }

    protected function normalizePhone(mixed $phone): ?string
    {
        if (! is_string($phone)) {
            return null;
        }

        $phone = preg_replace('/\D+/', '', $phone) ?? '';

        return $phone !== '' ? $phone : null;
    }

    protected function attachCustomerRole(User $user): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('user_roles')) {
            return;
        }

        $customerRole = Role::query()->where('name', 'customer')->first();

        if ($customerRole === null) {
            return;
        }

        UserRole::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'role_id' => $customerRole->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
