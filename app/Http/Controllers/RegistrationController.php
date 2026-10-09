<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\AlreadyRegisteredException;
use App\Exceptions\RegistrationClosedException;
use App\Exceptions\WorkshopFullException;
use App\Http\Requests\CancelRegistrationRequest;
use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Registration;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {}

    public function store(RegisterAttendeeRequest $request, Workshop $workshop): RedirectResponse
    {
        $this->authorize('create', [Registration::class, $workshop]);

        try {
            $this->registrationService->register(
                $workshop,
                $request->validated('attendee_name'),
                $request->validated('attendee_email'),
                $request->user()
            );
        } catch (WorkshopFullException $e) {
            return back()->withErrors(['attendee_email' => $e->getMessage()]);
        } catch (AlreadyRegisteredException $e) {
            return back()->withErrors(['attendee_email' => $e->getMessage()]);
        } catch (RegistrationClosedException $e) {
            return back()->withErrors(['attendee_email' => $e->getMessage()]);
        }

        return redirect()
            ->route('workshops.show', $workshop)
            ->with('success', 'Attendee registered successfully.');
    }

    public function cancel(CancelRegistrationRequest $request, Registration $registration): RedirectResponse
    {
        $this->authorize('cancel', $registration);

        $this->registrationService->cancel(
            $registration,
            $request->user(),
            $request->validated('cancel_reason')
        );

        return redirect()
            ->route('workshops.show', $registration->workshop_id)
            ->with('success', 'Registration cancelled successfully.');
    }
}
