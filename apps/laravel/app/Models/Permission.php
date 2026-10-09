<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RoleHasPermission::class, 'permission_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions')->withTimestamps();
    }
}
