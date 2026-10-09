<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function exteriorCarUnits(): HasMany
    {
        return $this->hasMany(CarUnit::class, 'exterior_color_id');
    }

    public function interiorCarUnits(): HasMany
    {
        return $this->hasMany(CarUnit::class, 'interior_color_id');
    }
}
