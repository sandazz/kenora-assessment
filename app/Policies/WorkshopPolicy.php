<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Workshop;

class WorkshopPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->canViewWorkshops();
    }

    public function view(User $user, Workshop $workshop): bool
    {
        return $user->role->canViewWorkshops();
    }

    public function create(User $user): bool
    {
        return $user->role->canManageWorkshops();
    }

    public function update(User $user, Workshop $workshop): bool
    {
        return $user->role->canManageWorkshops();
    }

    public function delete(User $user, Workshop $workshop): bool
    {
        return $user->role->canManageWorkshops();
    }
}
