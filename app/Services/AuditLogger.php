<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function log(string $action, Model $subject, array $changes = []): void
    {
        // Never log passwords
        if (isset($changes['before']['password'])) {
            unset($changes['before']['password']);
        }
        if (isset($changes['after']['password'])) {
            unset($changes['after']['password']);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->getKey(),
            'changes' => empty($changes) ? null : $changes,
            'ip_address' => Request::ip(),
        ]);
    }
}
