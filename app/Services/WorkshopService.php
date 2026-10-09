<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RegistrationStatus;
use App\Enums\WorkshopStatus;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WorkshopService
{
    public function __construct(
        private readonly AuditLogger $auditLogger
    ) {}

    public function create(array $data, User $actor): Workshop
    {
        // Default ends_at to starts_at + 2h if not provided
        if (empty($data['ends_at'])) {
            $data['ends_at'] = (clone Carbon::parse($data['starts_at']))->addHours(2);
        }

        $workshop = Workshop::create([
            ...$data,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
            'status' => $data['status'] ?? WorkshopStatus::Scheduled,
        ]);

        $this->auditLogger->log('workshop.created', $workshop, [
            'after' => $workshop->only(['code', 'title', 'location', 'capacity', 'status']),
        ]);

        return $workshop;
    }

    public function update(Workshop $workshop, array $data, User $actor): Workshop
    {
        return DB::transaction(function () use ($workshop, $data, $actor) {
            // Lock workshop row before capacity check
            $locked = Workshop::whereKey($workshop->id)->lockForUpdate()->firstOrFail();

            if (isset($data['capacity'])) {
                $activeCount = $locked->registrations()
                    ->where('status', RegistrationStatus::Active)
                    ->count();

                if ((int) $data['capacity'] < $activeCount) {
                    throw new InvalidArgumentException(
                        "Cannot reduce capacity below the current number of active registrations ({$activeCount})."
                    );
                }
            }

            // Default ends_at if not provided
            if (isset($data['starts_at']) && empty($data['ends_at'])) {
                $data['ends_at'] = (clone Carbon::parse($data['starts_at']))->addHours(2);
            }

            $beforeFull = $locked->only(['code', 'title', 'instructor', 'location', 'description', 'starts_at', 'ends_at', 'capacity', 'status']);

            $locked->update([
                ...$data,
                'updated_by' => $actor->id,
            ]);

            $afterFull = $locked->fresh()->only(['code', 'title', 'instructor', 'location', 'description', 'starts_at', 'ends_at', 'capacity', 'status']);

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
                if ($oldVal instanceof \DateTimeInterface) {
                    $oldVal = $oldVal->format('Y-m-d H:i:s');
                }
                if ($newVal instanceof \DateTimeInterface) {
                    $newVal = $newVal->format('Y-m-d H:i:s');
                }
                if ((string) $oldVal !== (string) $newVal) {
                    $before[$key] = $oldVal;
                    $after[$key] = $newVal;
                }
            }

            $this->auditLogger->log('workshop.updated', $locked, [
                'before' => $before,
                'after' => $after,
            ]);

            return $locked->fresh();
        }, attempts: 3);
    }
}
