<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly AuditLogger $auditLogger
    ) {}

    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => Role::from($data['role']),
            'is_active' => true,
        ]);

        $this->auditLogger->log('user.created', $user, [
            'after' => ['name' => $user->name, 'email' => $user->email, 'role' => $user->role->value],
        ]);

        return $user;
    }

    public function update(User $user, array $data, User $actor): User
    {
        // Safety: admin cannot demote or deactivate themselves
        if ($user->id === $actor->id) {
            if (isset($data['role']) && Role::from($data['role']) !== Role::Admin) {
                throw new \InvalidArgumentException('You cannot change your own role.');
            }
            if (isset($data['is_active']) && ! $data['is_active']) {
                throw new \InvalidArgumentException('You cannot deactivate your own account.');
            }
        }

        // Safety: last active admin cannot be demoted or deactivated
        if ($user->role === Role::Admin) {
            $activeAdminCount = User::where('role', Role::Admin)->where('is_active', true)->count();

            if ($activeAdminCount <= 1) {
                if (isset($data['role']) && Role::from($data['role']) !== Role::Admin) {
                    throw new \InvalidArgumentException('Cannot demote the last active administrator.');
                }
                if (isset($data['is_active']) && ! $data['is_active']) {
                    throw new \InvalidArgumentException('Cannot deactivate the last active administrator.');
                }
            }
        }

        $beforeFull = $user->only(['name', 'email', 'role', 'is_active']);

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['role'])) {
            $user->role = Role::from($data['role']);
        }
        if (array_key_exists('is_active', $data)) {
            $user->is_active = (bool) $data['is_active'];
        }
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $afterFull = $user->fresh()->only(['name', 'email', 'role', 'is_active']);

        $before = [];
        $after = [];

        foreach ($afterFull as $key => $val) {
            $oldVal = $beforeFull[$key] ?? null;
            $newVal = $val;
            if ($oldVal instanceof \BackedEnum) {
                $oldVal = $oldVal->value;
            }
            if ($newVal instanceof \BackedEnum) {
                $newVal = $newVal->value;
            }
            if ((string) $oldVal !== (string) $newVal) {
                $before[$key] = $oldVal;
                $after[$key] = $newVal;
            }
        }

        $changes = ['before' => $before, 'after' => $after];

        // Log role changes, deactivation, or general updates specifically
        if (array_key_exists('role', $before)) {
            $this->auditLogger->log('user.role_changed', $user, $changes);
        }
        if (array_key_exists('is_active', $before) && ! $user->is_active) {
            $this->auditLogger->log('user.deactivated', $user, $changes);
        } elseif (! empty($before)) {
            $this->auditLogger->log('user.updated', $user, $changes);
        }

        return $user->fresh();
    }
}
