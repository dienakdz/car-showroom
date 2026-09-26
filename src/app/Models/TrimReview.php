<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $trim_id
 * @property int $user_id
 * @property int $rating
 * @property string $title
 * @property string $content
 * @property string $status
 * @property string|null $user_name
 * @property-read User|null $user
 */
class TrimReview extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    public function scopeApproved(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', 'approved');
    }

    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class, 'trim_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
