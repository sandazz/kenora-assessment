<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Registration::class);

        $query = Registration::with(['workshop', 'registeredBy', 'cancelledBy'])
            ->orderByDesc('registered_at');

        if ($request->filled('workshop_id')) {
            $query->where('workshop_id', $request->input('workshop_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $registrations = $query->paginate(20)->withQueryString();
        $workshops = Workshop::orderBy('title')->get(['id', 'title', 'code']);
        $statuses = RegistrationStatus::cases();

        return view('registrations.index', compact('registrations', 'workshops', 'statuses'));
    }
}
