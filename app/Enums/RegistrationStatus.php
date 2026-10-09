<?php

declare(strict_types=1);

namespace App\Enums;

enum RegistrationStatus: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            RegistrationStatus::Active => 'Active',
            RegistrationStatus::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            RegistrationStatus::Active => 'badge-active',
            RegistrationStatus::Cancelled => 'badge-cancelled',
        };
    }
}
