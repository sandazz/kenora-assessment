<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Enums\WorkshopStatus;
use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class WorkshopController extends Controller
{
    public function __construct(
        private readonly WorkshopService $workshopService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Workshop::class);

        $query = Workshop::withCount([
            'registrations as active_registrations_count' => fn ($q) => $q->where('status', RegistrationStatus::Active),
        ]);

        // Default: upcoming scheduled workshops
        $defaultStatus = null;

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }
        if ($request->filled('from')) {
            $query->fromDate($request->input('from'));
        }
        if ($request->filled('to')) {
            $query->toDate($request->input('to'));
        }
        if ($request->filled('status')) {
            $query->withStatus($request->input('status'));
        }
        if ($request->filled('location')) {
            $query->forLocation($request->input('location'));
        }
        $minSeats = (int) ($request->input('min_seats') ?: 1);
        if ($request->boolean('available_only')) {
            $query->whereRaw(
                'capacity - (SELECT COUNT(*) FROM registrations WHERE registrations.workshop_id = workshops.id AND registrations.status = ?) >= ?',
                [RegistrationStatus::Active->value, $minSeats]
            );
        }

        // If no filters set, show upcoming scheduled by default
        if (! $request->hasAny(['q', 'from', 'to', 'status', 'location', 'available_only'])) {
            $query->where('starts_at', '>', now())->where('status', WorkshopStatus::Scheduled);
        }

        $workshops = $query->orderBy('starts_at')->paginate(15)->withQueryString();

        $locations = Workshop::distinct()->pluck('location')->sort()->values();
        $statuses = WorkshopStatus::cases();

        return view('workshops.index', compact('workshops', 'locations', 'statuses'));
    }

    public function show(Workshop $workshop): View
    {
        $this->authorize('view', $workshop);

        $workshop->load('creator', 'updater');
        $workshop->loadCount([
            'registrations as active_registrations_count' => fn ($q) => $q->where('status', RegistrationStatus::Active),
        ]);

        $activeRegistrations = $workshop->registrations()
            ->where('status', RegistrationStatus::Active)
            ->with('registeredBy')
            ->orderByDesc('registered_at')
            ->get();

        $allRegistrations = $workshop->registrations()
            ->with('registeredBy', 'cancelledBy')
            ->orderByDesc('registered_at')
            ->get();

        return view('workshops.show', compact('workshop', 'activeRegistrations', 'allRegistrations'));
    }

    public function create(): View
    {
        $this->authorize('create', Workshop::class);
        $locations = ['Downtown Centre', 'Westside Branch', 'Northgate Hub'];
        $statuses = WorkshopStatus::cases();

        return view('workshops.create', compact('locations', 'statuses'));
    }

    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        $workshop = $this->workshopService->create($request->validated(), $request->user());

        return redirect()
            ->route('workshops.show', $workshop)
            ->with('success', "Workshop \"{$workshop->title}\" created successfully.");
    }

    public function edit(Workshop $workshop): View
    {
        $this->authorize('update', $workshop);
        $locations = ['Downtown Centre', 'Westside Branch', 'Northgate Hub'];
        $statuses = WorkshopStatus::cases();

        return view('workshops.edit', compact('workshop', 'locations', 'statuses'));
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        try {
            $workshop = $this->workshopService->update($workshop, $request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['capacity' => $e->getMessage()]);
        }

        return redirect()
            ->route('workshops.show', $workshop)
            ->with('success', "Workshop \"{$workshop->title}\" updated successfully.");
    }
}
