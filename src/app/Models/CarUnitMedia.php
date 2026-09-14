<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarUnitMedia extends EloquentModel
{
    use HasFactory;

    protected $table = 'car_unit_media';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
        ];
    }

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class, 'car_unit_id');
    }

    public function displayUrl(): ?string
    {
        $path = ltrim(trim((string) $this->path_or_url), '/');

        if ($path === '') {
            return null;
        }

        return file_exists(public_path($path)) ? asset($path) : null;
    }
}
