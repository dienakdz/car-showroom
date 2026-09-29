<?php

namespace App\Http\Controllers\Clients;

use App\Mail\CustomerActivationMail;
use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Trim;
use App\Models\TrimReview;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

    public function account(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return $this->viewWithSharedData('client.auth');
        }

        if ($user->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        $reviewModels = TrimReview::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('trim_id');

        $purchasedTrimIds = Sale::query()
            ->join('car_units', 'car_units.id', '=', 'sales.car_unit_id')
            ->where('sales.buyer_user_id', $user->id)
            ->whereNotNull('car_units.trim_id')
            ->distinct()
            ->pluck('car_units.trim_id');

        $upcomingAppointmentsCount = Appointment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->count();

        $nextAppointment = Appointment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->with([
                'carUnit.media' => fn ($query) => $query
                    ->where('type', 'image')
                    ->orderByDesc('is_cover')
                    ->orderBy('sort_order'),
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->orderBy('scheduled_at')
            ->first();

        $accountSummary = [
            'profileCompletion' => $this->calculateProfileCompletion($user),
            'leadCount' => Lead::query()->where('user_id', $user->id)->count(),
            'upcomingAppointmentsCount' => $upcomingAppointmentsCount,
            'purchaseCount' => Sale::query()->where('buyer_user_id', $user->id)->count(),
            'reviewCount' => $reviewModels->count(),
            'reviewableCount' => $purchasedTrimIds->diff($reviewModels->keys())->count(),
            'notificationCount' => $user->notifications()->count(),
            'unreadNotificationCount' => $user->unreadNotifications()->count(),
            'memberSinceLabel' => $user->created_at ? Carbon::parse($user->created_at)->format('d/m/Y') : 'Mới tham gia',
            'nextAppointment' => $nextAppointment ? $this->mapAccountAppointment($nextAppointment) : null,
        ];

        return $this->viewWithSharedData('client.account', [
            'accountSummary' => $accountSummary,
            'accountAppointments' => $this->loadAccountAppointments($user),
            'accountLeads' => $this->loadAccountLeads($user),
            'accountPurchases' => $this->loadAccountPurchases($user, $reviewModels),
            'accountReviews' => $this->loadAccountReviews($user),
            'accountNotifications' => $user->notifications()->latest()->paginate(10, ['*'], 'notif_page')->withQueryString(),
        ]);
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

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $request->merge([
            'email' => $this->normalizeEmail($request->input('email')),
            'phone' => $this->normalizePhone($request->input('phone')),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'form_mode' => ['nullable', 'string'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($this->normalizeEmail($request->input('email')) === null && $this->normalizePhone($request->input('phone')) === null) {
                $validator->errors()->add('email', 'Vui lòng nhập email hoặc số điện thoại.');
            }
        });

        $data = $validator->validate();

        $user->fill([
            'name' => trim((string) $data['name']),
            'email' => $this->normalizeEmail($data['email'] ?? null),
            'phone' => $this->normalizePhone($data['phone'] ?? null),
        ]);
        $user->save();

        $this->pushSuccessToast('Cập nhật thông tin cá nhân thành công.');

        return redirect()->route('account.show', ['tab' => 'profile']);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
            'form_mode' => ['nullable', 'string'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])
                ->withInput(['form_mode' => 'account_password']);
        }

        if (Hash::check($validated['new_password'], $user->password)) {
            return back()
                ->withErrors(['new_password' => 'Mật khẩu mới phải khác mật khẩu hiện tại.'])
                ->withInput(['form_mode' => 'account_password']);
        }

        $user->password = $validated['new_password'];
        $user->save();

        $this->pushSuccessToast('Đổi mật khẩu thành công.');

        return redirect()->route('account.show', ['tab' => 'profile']);
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

    protected function calculateProfileCompletion(User $user): int
    {
        $score = 34;

        if (filled($user->email)) {
            $score += 33;
        }

        if (filled($user->phone)) {
            $score += 33;
        }

        return min($score, 100);
    }

    protected function loadAccountLeads(User $user, int $limit = 8)
    {
        return Lead::query()
            ->where('user_id', $user->id)
            ->with([
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Lead $lead): object {
                /** @var CarUnit|null $carUnit */
                $carUnit = $lead->carUnit;
                /** @var Trim|null $contextTrim */
                $contextTrim = $carUnit !== null ? $carUnit->trim : $lead->trim;

                return (object) [
                    'id' => $lead->id,
                    'created_at_label' => $lead->created_at ? Carbon::parse($lead->created_at)->format('d/m/Y H:i') : 'Đang cập nhật',
                    'source_label' => $this->leadSourceLabel((string) $lead->source),
                    'status_label' => $this->leadStatusLabel((string) $lead->status),
                    'status_tone' => $this->leadStatusTone((string) $lead->status),
                    'context_label' => $this->formatCarContextLabel($lead->carUnit, $contextTrim),
                    'context_url' => $this->resolveAccountContextUrl($lead->carUnit, $contextTrim),
                    'message' => trim((string) ($lead->message ?? '')),
                ];
            });
    }

    protected function loadAccountAppointments(User $user, int $limit = 8)
    {
        return Appointment::query()
            ->where('user_id', $user->id)
            ->with([
                'carUnit.media' => fn ($query) => $query
                    ->where('type', 'image')
                    ->orderByDesc('is_cover')
                    ->orderBy('sort_order'),
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->orderByDesc('scheduled_at')
            ->limit($limit)
            ->get()
            ->map(fn (Appointment $appointment): object => $this->mapAccountAppointment($appointment));
    }

    protected function mapAccountAppointment(Appointment $appointment): object
    {
        /** @var CarUnit|null $carUnit */
        $carUnit = $appointment->carUnit;
        /** @var Trim|null $contextTrim */
        $contextTrim = $carUnit !== null ? $carUnit->trim : $appointment->trim;
        $coverMedia = $carUnit !== null ? $carUnit->media->first() : null;
        $coverMediaPath = $coverMedia instanceof CarUnitMedia ? (string) $coverMedia->path_or_url : null;

        return (object) [
            'id' => $appointment->id,
            'image_url' => $this->resolveMediaPath($coverMediaPath),
            'scheduled_at_label' => $appointment->scheduled_at ? Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') : 'Đang cập nhật',
            'status_label' => $this->appointmentStatusLabel((string) $appointment->status),
            'status_tone' => $this->appointmentStatusTone((string) $appointment->status),
            'context_label' => $this->formatCarContextLabel($appointment->carUnit, $contextTrim),
            'context_url' => $this->resolveAccountContextUrl($appointment->carUnit, $contextTrim),
            'note' => trim((string) ($appointment->note ?? '')),
        ];
    }

    protected function loadAccountPurchases(User $user, $reviewModelsByTrim, int $limit = 8)
    {
        return Sale::query()
            ->where('buyer_user_id', $user->id)
            ->with([
                'carUnit.media' => fn ($query) => $query
                    ->where('type', 'image')
                    ->orderByDesc('is_cover')
                    ->orderBy('sort_order'),
                'carUnit.trim.model.make',
            ])
            ->orderByDesc('sold_at')
            ->limit($limit)
            ->get()
            ->map(function (Sale $sale) use ($reviewModelsByTrim): object {
                /** @var CarUnit|null $carUnit */
                $carUnit = $sale->carUnit;
                /** @var Trim|null $trim */
                $trim = $carUnit !== null ? $carUnit->trim : null;
                $review = $trim ? $reviewModelsByTrim->get($trim->id) : null;
                $coverMedia = $carUnit !== null ? $carUnit->media->first() : null;
                $coverMediaPath = $coverMedia instanceof CarUnitMedia ? (string) $coverMedia->path_or_url : null;

                return (object) [
                    'id' => $sale->id,
                    'image_url' => $this->resolveMediaPath($coverMediaPath),
                    'car_label' => $this->formatCarContextLabel($sale->carUnit, $trim),
                    'trim_label' => $this->formatTrimLabel($trim),
                    'sold_at_label' => $sale->sold_at ? Carbon::parse($sale->sold_at)->format('d/m/Y') : 'Đang cập nhật',
                    'sold_price_label' => $sale->sold_price !== null
                        ? number_format((float) $sale->sold_price, 0, ',', '.') . ' VNĐ'
                        : 'Theo hợp đồng',
                    'trim_url' => $trim?->slug ? route('trim.show', ['trimSlug' => $trim->slug]) : route('inventory.index'),
                    'review_status_label' => $review ? $this->reviewStatusLabel((string) $review->status) : 'Chưa đánh giá',
                    'review_status_tone' => $review ? $this->reviewStatusTone((string) $review->status) : 'warning',
                    'can_review' => $trim !== null && $review === null,
                ];
            });
    }

    protected function loadAccountReviews(User $user, int $limit = 8)
    {
        return TrimReview::query()
            ->where('user_id', $user->id)
            ->with('trim.model.make')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (TrimReview $review): object {
                /** @var Trim|null $trim */
                $trim = $review->trim;

                return (object) [
                    'id' => $review->id,
                    'trim_label' => $this->formatTrimLabel($trim),
                    'trim_url' => $trim !== null && filled($trim->slug) ? route('trim.show', ['trimSlug' => $trim->slug]) : route('inventory.index'),
                    'rating' => (int) $review->rating,
                    'comment' => trim((string) $review->comment),
                    'status_label' => $this->reviewStatusLabel((string) $review->status),
                    'status_tone' => $this->reviewStatusTone((string) $review->status),
                    'created_at_label' => $review->created_at ? Carbon::parse($review->created_at)->format('d/m/Y') : 'Đang cập nhật',
                ];
            });
    }

    protected function resolveAccountContextUrl(mixed $carUnit, mixed $trim): string
    {
        if ($carUnit !== null
            && filled($carUnit->stock_code)
            && $carUnit->status === 'available'
            && $carUnit->published_at !== null) {
            return route('car.show', ['stockCode' => $carUnit->stock_code]);
        }

        if ($trim !== null && filled($trim->slug)) {
            return route('trim.show', ['trimSlug' => $trim->slug]);
        }

        return route('inventory.index');
    }

    protected function formatCarContextLabel(mixed $carUnit, mixed $trim = null): string
    {
        $resolvedTrim = ($carUnit instanceof CarUnit ? $carUnit->trim : null) ?? ($trim instanceof Trim ? $trim : null);
        $trimLabel = $this->formatTrimLabel($resolvedTrim);

        if ($carUnit !== null && filled($carUnit->stock_code)) {
            return $trimLabel . ' | Stock ' . $carUnit->stock_code;
        }

        return $trimLabel;
    }

    protected function formatTrimLabel(mixed $trim): string
    {
        if ($trim === null) {
            return 'Đang cập nhật phiên bản';
        }

        $makeName = $trim->model?->make?->name;
        $modelName = $trim->model?->name;
        $trimName = $trim->name;

        return trim(collect([$makeName, $modelName, $trimName])->filter()->implode(' '));
    }

    protected function leadSourceLabel(string $source): string
    {
        return match ($source) {
            'unit_detail' => 'Trang chi tiết xe',
            'trim_page' => 'Trang phiên bản xe',
            'finance' => 'Tư vấn tài chính trả góp',
            'trade_in' => 'Thẩm định thu cũ đổi mới',
            default => 'Liên hệ tư vấn chung',
        };
    }

    protected function leadStatusLabel(string $status): string
    {
        return match ($status) {
            'contacted' => 'Đã liên hệ tư vấn',
            'qualified' => 'Đã xác nhận nhu cầu',
            'booked' => 'Đã đặt lịch hẹn',
            'closed' => 'Giao dịch thành công',
            'lost' => 'Đã hủy yêu cầu',
            default => 'Đang chờ xử lý',
        };
    }

    protected function leadStatusTone(string $status): string
    {
        return match ($status) {
            'closed' => 'success',
            'booked', 'qualified' => 'info',
            'contacted' => 'neutral',
            'lost' => 'danger',
            default => 'warning',
        };
    }

    protected function appointmentStatusLabel(string $status): string
    {
        return match ($status) {
            'confirmed' => 'Đã xác nhận lịch',
            'done' => 'Đã hoàn tất',
            'cancelled' => 'Đã hủy lịch',
            default => 'Chờ showroom xác nhận',
        };
    }

    protected function appointmentStatusTone(string $status): string
    {
        return match ($status) {
            'confirmed' => 'info',
            'done' => 'success',
            'cancelled' => 'danger',
            default => 'warning',
        };
    }

    protected function reviewStatusLabel(string $status): string
    {
        return match ($status) {
            'approved' => 'Đã duyệt',
            'hidden' => 'Đã ẩn',
            default => 'Chờ duyệt',
        };
    }

    protected function reviewStatusTone(string $status): string
    {
        return match ($status) {
            'approved' => 'success',
            'hidden' => 'danger',
            default => 'warning',
        };
    }
}
