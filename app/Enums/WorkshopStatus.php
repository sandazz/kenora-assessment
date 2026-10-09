<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkshopStatus: string
{
    case Scheduled = 'scheduled';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            WorkshopStatus::Scheduled => 'Scheduled',
            WorkshopStatus::Cancelled => 'Cancelled',
            WorkshopStatus::Completed => 'Completed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            WorkshopStatus::Scheduled => 'badge-scheduled',
            WorkshopStatus::Cancelled => 'badge-cancelled',
            WorkshopStatus::Completed => 'badge-completed',
        };
    }

    public function isOpenForRegistration(): bool
    {
        return $this === WorkshopStatus::Scheduled;
    }
}
