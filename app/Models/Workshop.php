<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Enums\WorkshopStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'instructor',
        'location',
        'description',
        'starts_at',
        'ends_at',
        'capacity',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity' => 'integer',
            'status' => WorkshopStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function activeRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class)->where('status', RegistrationStatus::Active);
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === WorkshopStatus::Scheduled
            && $this->starts_at->isFuture();
    }

    public function seatsLeft(): int
    {
        $taken = $this->active_registrations_count ?? $this->activeRegistrations()->count();

        return max(0, $this->capacity - $taken);
    }

    // Query scopes
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', WorkshopStatus::Scheduled);
    }

    public function scopeFromDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('starts_at', '>=', $date);
    }

    public function scopeToDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('starts_at', '<=', $date);
    }

    public function scopeForLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', 'like', '%'.$location.'%');
    }

    public function scopeSearch(Builder $query, string $q): Builder
    {
        return $query->where(function (Builder $q2) use ($q) {
            $q2->where('code', 'like', '%'.$q.'%')
                ->orWhere('title', 'like', '%'.$q.'%')
                ->orWhere('instructor', 'like', '%'.$q.'%');
        });
    }

    public function scopeAvailableOnly(Builder $query): Builder
    {
        return $query->withCount(['registrations as active_registrations_count' => function (Builder $q) {
            $q->where('status', RegistrationStatus::Active);
        }])->havingRaw('capacity - active_registrations_count > 0');
    }

    public function scopeMinSeats(Builder $query, int $min): Builder
    {
        return $query->havingRaw('capacity - active_registrations_count >= ?', [$min]);
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
