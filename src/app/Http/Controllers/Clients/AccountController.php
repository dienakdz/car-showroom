<?php

namespace App\Http\Controllers\Clients;

use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Trim;
use App\Models\TrimReview;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends ClientBaseController
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->route('login');
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

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
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

        if (! $user instanceof User) {
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

    protected function loadAccountLeads(User $user, int $limit = 8): Collection
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

    protected function loadAccountAppointments(User $user, int $limit = 8): Collection
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

    protected function loadAccountPurchases(User $user, Collection $reviewModelsByTrim, int $limit = 8): Collection
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

    protected function loadAccountReviews(User $user, int $limit = 8): Collection
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
}
