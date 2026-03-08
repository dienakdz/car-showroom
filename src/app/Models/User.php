<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function trimReviews(): HasMany
    {
        return $this->hasMany(TrimReview::class, 'user_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'user_id');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function leadNotesCreated(): HasMany
    {
        return $this->hasMany(LeadNote::class, 'created_by');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function handledAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'handled_by');
    }

    public function carUnitHoldsCreated(): HasMany
    {
        return $this->hasMany(CarUnitHold::class, 'created_by');
    }

    public function carUnitPriceChanges(): HasMany
    {
        return $this->hasMany(CarUnitPriceHistory::class, 'changed_by');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Sale::class, 'buyer_user_id');
    }

    public function salesCreated(): HasMany
    {
        return $this->hasMany(Sale::class, 'created_by');
    }
}
