<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarUnitHold extends EloquentModel
{
    use HasFactory;

    protected $table = 'car_unit_holds';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'hold_until' => 'datetime',
        ];
    }

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class, 'car_unit_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
