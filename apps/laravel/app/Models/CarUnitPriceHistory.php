<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarUnitPriceHistory extends EloquentModel
{
    use HasFactory;

    protected $table = 'car_unit_price_histories';

    protected $guarded = [];

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class, 'car_unit_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
