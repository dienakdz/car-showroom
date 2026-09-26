<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $label
 * @property string $type
 * @property string|null $unit
 * @property int $sort_order
 * @property bool $is_filterable
 */
class CarAttribute extends EloquentModel
{
    use HasFactory;

    protected $table = 'attributes';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_filterable' => 'boolean',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(TrimAttributeValue::class, 'attribute_id');
    }
}
