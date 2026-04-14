<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Make extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    public function models(): HasMany
    {
        return $this->hasMany(CarModel::class, 'make_id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        $path = $this->logo_path;

        if ($path === null || $path === '') {
            return null;
        }

        if (preg_match('/^https?:\\/\\//i', $path) === 1) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'boxcar/')) {
            return asset($cleanPath);
        }

        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        if (file_exists(public_path('boxcar/' . $cleanPath))) {
            return asset('boxcar/' . $cleanPath);
        }

        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::disk('public')->url($cleanPath);
        }

        return asset($cleanPath);
    }

    public function getInitialsAttribute(): string
    {
        $label = trim((string) $this->name);

        if ($label === '') {
            return '?';
        }

        $parts = collect(preg_split('/\\s+/', $label, -1, PREG_SPLIT_NO_EMPTY))
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $parts !== '' ? $parts : Str::upper(Str::substr($label, 0, 1));
    }
}
