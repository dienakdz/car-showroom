<?php

namespace App\Services\Admin;

use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                    'car_unit_id' => 'Xe nay da co sale va khong the chot them lan nua.',
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

            if (! empty($validated['lead_id'])) {
                Lead::query()
                    ->whereKey($validated['lead_id'])
                    ->update(['status' => 'closed']);
            }

            return $sale->fresh([
                'buyer',
                'carUnit.trim.model.make',
                'createdBy',
            ]);
        });
    }

    protected function resolveBuyer(array $validated): User
    {
        if (! empty($validated['buyer_user_id'])) {
            return User::query()->findOrFail($validated['buyer_user_id']);
        }

        $email = $validated['buyer_email'] ?? null;
        $phone = $validated['buyer_phone'] ?? null;
        $name = trim((string) ($validated['buyer_name'] ?? 'Khach hang showroom'));

        $buyer = null;

        if ($email !== null) {
            $buyer = User::query()->where('email', $email)->first();
        }

        if ($buyer === null && $phone !== null) {
            $buyer = User::query()->where('phone', $phone)->first();
        }

        if ($buyer !== null) {
            $buyer->fill(array_filter([
                'name' => $buyer->name ?: $name,
                'email' => $buyer->email ?: $email,
                'phone' => $buyer->phone ?: $phone,
            ], fn ($value) => $value !== null));
            $buyer->save();

            return $buyer;
        }

        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Str::random(24),
        ]);
    }
}
