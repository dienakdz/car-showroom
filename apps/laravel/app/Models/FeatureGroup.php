<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureGroup extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'feature_group_id');
    }
}
