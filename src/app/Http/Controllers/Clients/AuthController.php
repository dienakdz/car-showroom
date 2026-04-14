<?php

namespace App\Http\Controllers\Clients;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Sale;
use App\Models\TrimReview;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends ClientBaseController
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('account.show');
        }

        return $this->viewWithSharedData('client.auth');
    }

    public function account(): View
    {
        $user = Auth::user();

        if ($user === null) {
            return $this->viewWithSharedData('client.auth');
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
            'memberSinceLabel' => optional($user->created_at)->format('d/m/Y') ?? 'Moi tham gia',
            'nextAppointment' => $nextAppointment ? $this->mapAccountAppointment($nextAppointment) : null,
        ];

        return $this->viewWithSharedData('client.auth', [
            'accountSummary' => $accountSummary,
            'accountAppointments' => $this->loadAccountAppointments($user),
            'accountLeads' => $this->loadAccountLeads($user),
            'accountPurchases' => $this->loadAccountPurchases($user, $reviewModels),
            'accountReviews' => $this->loadAccountReviews($user),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
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
                ->withErrors(['identifier' => 'Thong tin dang nhap khong dung.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $this->pushSuccessToast('Dang nhap thanh cong.');

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

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'accept_privacy' => ['accepted'],
            'form_mode' => ['nullable', 'string'],
        ], [
            'accept_privacy.accepted' => 'Ban can dong y voi chinh sach bao mat de tao tai khoan.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($this->normalizeEmail($request->input('email')) === null && $this->normalizePhone($request->input('phone')) === null) {
                $validator->errors()->add('email', 'Vui long nhap email hoac so dien thoai.');
            }
        });

        $data = $validator->validate();

        $user = User::query()->create([
            'name' => trim((string) $data['name']),
            'email' => $this->normalizeEmail($data['email'] ?? null),
            'phone' => $this->normalizePhone($data['phone'] ?? null),
            'password' => $data['password'],
        ]);

        $this->attachCustomerRole($user);

        Auth::login($user);
        $request->session()->regenerate();
        $this->pushSuccessToast('Tao tai khoan thanh cong.');

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $this->pushSuccessToast('Da dang xuat.');
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
                $validator->errors()->add('email', 'Vui long nhap email hoac so dien thoai.');
            }
        });

        $data = $validator->validate();

        $user->fill([
            'name' => trim((string) $data['name']),
            'email' => $this->normalizeEmail($data['email'] ?? null),
            'phone' => $this->normalizePhone($data['phone'] ?? null),
        ]);
        $user->save();

        $this->pushSuccessToast('Cap nhat thong tin ca nhan thanh cong.');

        return redirect()->route('account.show', ['tab' => 'account-profile']);
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
                ->withErrors(['current_password' => 'Mat khau hien tai khong dung.'])
                ->withInput(['form_mode' => 'account_password']);
        }

        if (Hash::check($validated['new_password'], $user->password)) {
            return back()
                ->withErrors(['new_password' => 'Mat khau moi phai khac mat khau hien tai.'])
                ->withInput(['form_mode' => 'account_password']);
        }

        $user->password = $validated['new_password'];
        $user->save();

        $this->pushSuccessToast('Doi mat khau thanh cong.');

        return redirect()->route('account.show', ['tab' => 'account-profile']);
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
                $contextTrim = $lead->carUnit?->trim ?? $lead->trim;

                return (object) [
                    'id' => $lead->id,
                    'created_at_label' => optional($lead->created_at)->format('d/m/Y H:i') ?? 'Dang cap nhat',
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
        $contextTrim = $appointment->carUnit?->trim ?? $appointment->trim;

        return (object) [
            'id' => $appointment->id,
            'scheduled_at_label' => optional($appointment->scheduled_at)->format('d/m/Y H:i') ?? 'Dang cap nhat',
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
                $trim = $sale->carUnit?->trim;
                $review = $trim ? $reviewModelsByTrim->get($trim->id) : null;
                $coverMediaPath = $sale->carUnit?->media->first()?->path_or_url;

                return (object) [
                    'id' => $sale->id,
                    'image_url' => $this->resolveMediaPath($coverMediaPath),
                    'car_label' => $this->formatCarContextLabel($sale->carUnit, $trim),
                    'trim_label' => $this->formatTrimLabel($trim),
                    'sold_at_label' => optional($sale->sold_at)->format('d/m/Y') ?? 'Dang cap nhat',
                    'sold_price_label' => $sale->sold_price !== null
                        ? number_format((float) $sale->sold_price, 0, ',', '.') . ' VND'
                        : 'Theo hop dong',
                    'trim_url' => $trim?->slug ? route('trim.show', ['trimSlug' => $trim->slug]) : route('inventory.index'),
                    'review_status_label' => $review ? $this->reviewStatusLabel((string) $review->status) : 'Chua danh gia',
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
                return (object) [
                    'id' => $review->id,
                    'trim_label' => $this->formatTrimLabel($review->trim),
                    'trim_url' => $review->trim?->slug ? route('trim.show', ['trimSlug' => $review->trim->slug]) : route('inventory.index'),
                    'rating' => (int) $review->rating,
                    'comment' => trim((string) $review->comment),
                    'status_label' => $this->reviewStatusLabel((string) $review->status),
                    'status_tone' => $this->reviewStatusTone((string) $review->status),
                    'created_at_label' => optional($review->created_at)->format('d/m/Y') ?? 'Dang cap nhat',
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
        $resolvedTrim = $carUnit?->trim ?? $trim;
        $trimLabel = $this->formatTrimLabel($resolvedTrim);

        if ($carUnit !== null && filled($carUnit->stock_code)) {
            return $trimLabel . ' | Stock ' . $carUnit->stock_code;
        }

        return $trimLabel;
    }

    protected function formatTrimLabel(mixed $trim): string
    {
        if ($trim === null) {
            return 'Dang cap nhat phien ban';
        }

        $makeName = $trim->model?->make?->name;
        $modelName = $trim->model?->name;
        $trimName = $trim->name;

        return trim(collect([$makeName, $modelName, $trimName])->filter()->implode(' '));
    }

    protected function leadSourceLabel(string $source): string
    {
        return match ($source) {
            'unit_detail' => 'Tu trang chi tiet xe',
            'trim_page' => 'Tu trang phien ban',
            'finance' => 'Tu van tai chinh',
            'trade_in' => 'Thu cu doi moi',
            default => 'Lien he chung',
        };
    }

    protected function leadStatusLabel(string $status): string
    {
        return match ($status) {
            'contacted' => 'Da lien he',
            'qualified' => 'Da xac thuc nhu cau',
            'booked' => 'Da dat lich',
            'closed' => 'Da chot',
            'lost' => 'Khong chot',
            default => 'Moi tao',
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
            'confirmed' => 'Da xac nhan',
            'done' => 'Da hoan tat',
            'cancelled' => 'Da huy',
            default => 'Cho xac nhan',
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
            'approved' => 'Da duyet',
            'hidden' => 'Da an',
            default => 'Cho duyet',
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
