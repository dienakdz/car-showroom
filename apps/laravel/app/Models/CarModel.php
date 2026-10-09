<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends EloquentModel
{
    use HasFactory;

    protected $table = 'models';

    protected $guarded = [];

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class, 'make_id');
    }

    public function trims(): HasMany
    {
        return $this->hasMany(Trim::class, 'model_id');
    }
}
