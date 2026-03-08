<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function group(): BelongsTo
    {
        return $this->belongsTo(FeatureGroup::class, 'feature_group_id');
    }

    public function trims(): BelongsToMany
    {
        return $this->belongsToMany(Trim::class, 'trim_feature');
    }
}
