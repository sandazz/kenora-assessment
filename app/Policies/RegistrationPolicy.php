<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

class RegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->canHandleRegistrations();
    }

    public function create(User $user, Workshop $workshop): bool
    {
        return $user->role->canHandleRegistrations();
    }

    public function cancel(User $user, Registration $registration): bool
    {
        return $user->role->canHandleRegistrations();
    }
}
