<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function workshopsCreated(): HasMany
    {
        return $this->hasMany(Workshop::class, 'created_by');
    }

    public function workshopsUpdated(): HasMany
    {
        return $this->hasMany(Workshop::class, 'updated_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'registered_by');
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(Registration::class, 'cancelled_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === Role::Manager;
    }

    public function isStaff(): bool
    {
        return $this->role === Role::Staff;
    }
}
