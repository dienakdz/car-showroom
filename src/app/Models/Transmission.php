<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transmission extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function carUnits(): HasMany
    {
        return $this->hasMany(CarUnit::class, 'transmission_id');
    }
}
