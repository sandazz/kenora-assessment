<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->role->canManageUsers();
    }

    public function create(User $actor): bool
    {
        return $actor->role->canManageUsers();
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->role->canManageUsers();
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->role->canManageUsers();
    }
}
