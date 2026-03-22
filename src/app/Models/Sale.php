<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    public static function hasBuyerPurchasedTrim(int $buyerUserId, int $trimId): bool
    {
        return static::query()
            ->join('car_units', 'car_units.id', '=', 'sales.car_unit_id')
            ->where('sales.buyer_user_id', $buyerUserId)
            ->where('car_units.trim_id', $trimId)
            ->exists();
    }

    protected function casts(): array
    {
        return [
            'sold_at' => 'datetime',
        ];
    }

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class, 'car_unit_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
