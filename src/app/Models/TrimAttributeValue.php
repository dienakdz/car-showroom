<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrimAttributeValue extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value_number' => 'decimal:4',
            'value_boolean' => 'boolean',
        ];
    }

    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class, 'trim_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CarAttribute::class, 'attribute_id');
    }
}
