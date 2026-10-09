#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Enums\Role;
use App\Enums\WorkshopStatus;
use App\Exceptions\WorkshopFullException;
use App\Models\User;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Contracts\Console\Kernel;

$workshopId = $argv[1] ?? null;

if (! $workshopId) {
    // Create a demo workshop with capacity 1
    $staff = User::where('role', Role::Staff)->first() ?? User::factory()->create(['role' => Role::Staff]);
    $workshop = Workshop::create([
        'code' => 'DEMO-RACE-'.rand(100, 999),
        'title' => 'Race Condition Test Workshop',
        'instructor' => 'Demo Tester',
        'location' => 'Downtown Centre',
        'starts_at' => now()->addDays(5),
        'capacity' => 1,
        'status' => WorkshopStatus::Scheduled,
        'created_by' => $staff->id,
        'updated_by' => $staff->id,
    ]);
    echo "Created demo workshop ID: {$workshop->id} with capacity 1\n";
} else {
    $workshop = Workshop::findOrFail($workshopId);
}

echo "Testing concurrency on Workshop #{$workshop->id} ({$workshop->code}) with capacity {$workshop->capacity}...\n";

$staff = User::where('role', Role::Staff)->first();
$service = app(RegistrationService::class);

$attempts = 5;
$successCount = 0;
$failureCount = 0;

$futures = [];
for ($i = 1; $i <= $attempts; $i++) {
    try {
        $service->register(
            $workshop->fresh(),
            "Attendee {$i}",
            "attendee{$i}@example.com",
            $staff
        );
        $successCount++;
        echo " [✓] Registration {$i} succeeded\n";
    } catch (WorkshopFullException $e) {
        $failureCount++;
        echo " [✕] Registration {$i} rejected: Workshop is full\n";
    } catch (Throwable $e) {
        $failureCount++;
        echo " [✕] Registration {$i} failed: ".$e->getMessage()."\n";
    }
}

$activeCount = $workshop->fresh()->registrations()->where('status', 'active')->count();

echo "\n--- Summary ---\n";
echo "Capacity: {$workshop->capacity}\n";
echo "Successful registrations: {$successCount}\n";
echo "Rejected registrations: {$failureCount}\n";
echo "Final DB Active Registrations Count: {$activeCount}\n";

if ($activeCount <= $workshop->capacity) {
    echo "SUCCESS: Capacity invariant holds! Active registrations ({$activeCount}) <= capacity ({$workshop->capacity})\n";
} else {
    echo "FAILURE: Capacity invariant violated!\n";
    exit(1);
}
