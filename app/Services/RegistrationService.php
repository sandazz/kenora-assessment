<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RegistrationStatus;
use App\Enums\WorkshopStatus;
use App\Exceptions\AlreadyRegisteredException;
use App\Exceptions\RegistrationClosedException;
use App\Exceptions\WorkshopFullException;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function __construct(
        private readonly AuditLogger $auditLogger
    ) {}

    /**
     * Register an attendee for a workshop with full concurrency protection.
     *
     * @throws WorkshopFullException
     * @throws AlreadyRegisteredException
     * @throws RegistrationClosedException
     */
    public function register(Workshop $workshop, string $name, string $email, User $actor): Registration
    {
        $email = strtolower(trim($email));

        return DB::transaction(function () use ($workshop, $name, $email, $actor) {
            // Re-load with a pessimistic write lock to serialise concurrent registrations
            $locked = Workshop::whereKey($workshop->id)->lockForUpdate()->firstOrFail();

            // Check status and start time
            if ($locked->status !== WorkshopStatus::Scheduled) {
                throw new RegistrationClosedException('Registration is closed for this workshop.');
            }

            if ($locked->starts_at->isPast()) {
                throw new RegistrationClosedException('This workshop has already started or ended.');
            }

            // Count active registrations inside the lock
            $activeCount = $locked->registrations()->where('status', RegistrationStatus::Active)->count();

            if ($activeCount >= $locked->capacity) {
                throw new WorkshopFullException(
                    'This workshop is full — 0 seats left.'
                );
            }

            try {
                $registration = Registration::create([
                    'workshop_id' => $locked->id,
                    'attendee_name' => $name,
                    'attendee_email' => $email,
                    'status' => RegistrationStatus::Active,
                    'registered_by' => $actor->id,
                    'registered_at' => now(),
                    'active_key' => 1,
                ]);
            } catch (QueryException $e) {
                // Unique constraint violation on (workshop_id, attendee_email, active_key)
                $code = (string) $e->errorInfo[1];
                if ($code === '1062' || $code === '19' || str_contains($e->getMessage(), 'UNIQUE')) {
                    throw new AlreadyRegisteredException('This email is already registered for this workshop.');
                }
                throw $e;
            }

            $this->auditLogger->log('registration.created', $registration, [
                'workshop_id' => $locked->id,
                'attendee_email' => $email,
            ]);

            return $registration;
        }, attempts: 3);
    }

    /**
     * Cancel a registration. Idempotent — cancelling an already-cancelled one is a no-op.
     * Never deletes the record.
     */
    public function cancel(Registration $registration, User $actor, ?string $reason = null): Registration
    {
        return DB::transaction(function () use ($registration, $actor, $reason) {
            // Lock the row
            $locked = Registration::whereKey($registration->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === RegistrationStatus::Cancelled) {
                // Idempotent — already cancelled, nothing to do
                return $locked;
            }

            $locked->update([
                'status' => RegistrationStatus::Cancelled,
                'cancelled_by' => $actor->id,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
                'active_key' => null,
            ]);

            $this->auditLogger->log('registration.cancelled', $locked, [
                'attendee_email' => $locked->attendee_email,
                'cancel_reason' => $reason,
            ]);

            return $locked;
        }, attempts: 3);
    }
}
