<?php

namespace App\Services\Admin;

use App\Models\CarModel;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Make;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Trim;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleManagementService
{
    public function create(array $validated, User $actor): Sale
    {
        return DB::transaction(function () use ($validated, $actor): Sale {
            $carUnit = CarUnit::query()
                ->with('sale')
                ->lockForUpdate()
                ->findOrFail($validated['car_unit_id']);

            if ($carUnit->sale !== null || $carUnit->status === 'sold') {
                throw ValidationException::withMessages([
                    'car_unit_id' => 'Xe này đã có hợp đồng bán và không thể chốt thêm lần nữa.',
                ]);
            }

            $buyer = $this->resolveBuyer($validated);

            $sale = Sale::query()->create([
                'car_unit_id' => $carUnit->id,
                'buyer_user_id' => $buyer->id,
                'created_by' => $actor->id,
                'sold_price' => $validated['sold_price'] ?? $carUnit->price,
                'sold_at' => $validated['sold_at'],
            ]);

            $carUnit->update([
                'status' => 'sold',
                'sold_at' => $validated['sold_at'],
                'hold_until' => null,
            ]);

            $leadId = $validated['lead_id'] ?? null;
            if (empty($leadId) && $buyer->id) {
                $leadId = Lead::query()
                    ->where('user_id', $buyer->id)
                    ->where('car_unit_id', $carUnit->id)
                    ->whereNotIn('status', ['closed', 'lost'])
                    ->latest('id')
                    ->value('id');
            }

            if (! empty($leadId)) {
                Lead::query()
                    ->whereKey($leadId)
                    ->update(['status' => 'closed']);
            }

            try {
                $buyerName = $buyer->name ?? 'Khách hàng';
                $saleCode = '#HD-' . str_pad((string) $sale->id, 4, '0', STR_PAD_LEFT);
                $amountFormatted = number_format((float) ($sale->sold_price ?? 0), 0, ',', '.') . ' VND';
                app(\App\Services\Admin\NotificationService::class)->notifyAdmins(
                    'sale',
                    'Hợp đồng mua bán đã hoàn tất',
                    "Hợp đồng {$saleCode} của {$buyerName} giá trị {$amountFormatted}.",
                    route('admin.sales.index'),
                    'fa fa-handshake',
                    ['sale_id' => $sale->id, 'contract_code' => $saleCode]
                );

                // Gửi thông báo chúc mừng tới tài khoản khách hàng
                $carUnit->loadMissing('trim.model.make');
                /** @var Trim|null $trim */
                $trim = $carUnit->trim;
                /** @var CarModel|null $model */
                $model = $trim?->model;
                /** @var Make|null $make */
                $make = $model?->make;

                $makeName = $make !== null ? $make->name : '';
                $trimName = $trim !== null ? $trim->name : '';
                $carTitle = trim(($carUnit->year ? $carUnit->year . ' ' : '') . $makeName . ' ' . $trimName);
                if ($carTitle === '') {
                    $carTitle = $carUnit->stock_code;
                }

                app(\App\Services\Admin\NotificationService::class)->notifyUser(
                    $buyer,
                    'sale',
                    'Chúc mừng bạn đã sở hữu xe ' . $carTitle,
                    "Hợp đồng mua xe {$carTitle} (Mã kho: {$carUnit->stock_code}) đã được Showroom hoàn tất ghi nhận. Kính chúc bạn vạn dặm bình an!",
                    route('account.show', ['tab' => 'purchases']),
                    'fa-solid fa-car-side',
                    ['sale_id' => $sale->id, 'car_unit_id' => $carUnit->id]
                );
            } catch (\Throwable) {
            }

            $fresh = $sale->fresh([
                'buyer',
                'carUnit.trim.model.make',
                'createdBy',
            ]);

            return $fresh ?? $sale;
        });
    }

    protected function resolveBuyer(array $validated): User
    {
        if (! empty($validated['buyer_user_id'])) {
            return User::query()->findOrFail($validated['buyer_user_id']);
        }

        $email = ! empty($validated['buyer_email']) ? trim((string) $validated['buyer_email']) : null;
        $phone = ! empty($validated['buyer_phone']) ? trim((string) $validated['buyer_phone']) : null;
        $name = filled($validated['buyer_name'] ?? null) ? trim((string) $validated['buyer_name']) : 'Khách hàng showroom';

        $buyer = null;
        if ($email || $phone) {
            $buyer = User::query()
                ->where(function ($q) use ($email, $phone): void {
                    if ($email) {
                        $q->where('email', $email);
                    }
                    if ($phone) {
                        $email ? $q->orWhere('phone', $phone) : $q->where('phone', $phone);
                    }
                })
                ->first();
        }

        if ($buyer !== null) {
            $updates = [];
            if (empty($buyer->name) && $name !== 'Khách hàng showroom') {
                $updates['name'] = $name;
            }
            if (empty($buyer->email) && $email && ! User::query()->where('email', $email)->where('id', '!=', $buyer->id)->exists()) {
                $updates['email'] = $email;
            }
            if (empty($buyer->phone) && $phone && ! User::query()->where('phone', $phone)->where('id', '!=', $buyer->id)->exists()) {
                $updates['phone'] = $phone;
            }

            if (! empty($updates)) {
                $buyer->update($updates);
            }

            return $buyer;
        }

        $defaultPassword = config('showroom.default_customer_password');

        $newUser = User::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $defaultPassword,
        ]);

        $customerRoleId = Role::query()->where('name', 'customer')->value('id');
        if ($customerRoleId) {
            UserRole::query()->firstOrCreate([
                'user_id' => $newUser->id,
                'role_id' => $customerRoleId,
            ]);
        }

        return $newUser;
    }
}
