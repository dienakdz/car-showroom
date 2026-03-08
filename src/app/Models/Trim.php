<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trim extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function carUnits(): HasMany
    {
        return $this->hasMany(CarUnit::class, 'trim_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'trim_feature');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(TrimAttributeValue::class, 'trim_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TrimReview::class, 'trim_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'trim_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'trim_id');
    }
}
