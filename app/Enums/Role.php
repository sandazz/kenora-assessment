<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            Role::Admin => 'Administrator',
            Role::Manager => 'Programme Manager',
            Role::Staff => 'Front Desk Staff',
        };
    }

    public function canManageUsers(): bool
    {
        return $this === Role::Admin;
    }

    public function canManageWorkshops(): bool
    {
        return $this === Role::Manager;
    }

    public function canHandleRegistrations(): bool
    {
        return match ($this) {
            Role::Manager, Role::Staff => true,
            default => false,
        };
    }

    public function canViewWorkshops(): bool
    {
        return match ($this) {
            Role::Manager, Role::Staff => true,
            default => false,
        };
    }
}
