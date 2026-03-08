<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarUnit extends EloquentModel
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'hold_until' => 'datetime',
            'published_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function scopeAvailable(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', 'available');
    }

    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class, 'trim_id');
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class, 'body_type_id');
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function transmission(): BelongsTo
    {
        return $this->belongsTo(Transmission::class, 'transmission_id');
    }

    public function drivetrain(): BelongsTo
    {
        return $this->belongsTo(Drivetrain::class, 'drivetrain_id');
    }

    public function exteriorColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'exterior_color_id');
    }

    public function interiorColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'interior_color_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(CarUnitMedia::class, 'car_unit_id');
    }

    public function holds(): HasMany
    {
        return $this->hasMany(CarUnitHold::class, 'car_unit_id');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(CarUnitPriceHistory::class, 'car_unit_id');
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'car_unit_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'car_unit_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'car_unit_id');
    }
}
