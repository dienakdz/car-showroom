<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Make extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    public function models(): HasMany
    {
        return $this->hasMany(CarModel::class, 'make_id');
    }
}
