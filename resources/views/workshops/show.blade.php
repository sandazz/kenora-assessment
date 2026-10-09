<x-app-layout>
    <x-slot name="title">{{ $workshop->title }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs font-mono text-gray-500 mb-0.5">{{ $workshop->code }}</div>
                <h1 class="text-xl font-semibold text-gray-900">{{ $workshop->title }}</h1>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $workshop)
                    <a href="{{ route('workshops.edit', $workshop) }}"
                       class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Edit Workshop
                    </a>
                @endcan
                <a href="{{ route('workshops.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← All Workshops</a>
            </div>
        </div>
    </x-slot>

    {{-- Cancelled/Completed Banner --}}
    @if($workshop->status->value !== 'scheduled')
        <div class="mb-4 rounded-md p-4 border
            {{ $workshop->status->value === 'cancelled' ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }}">
            <p class="text-sm font-medium {{ $workshop->status->value === 'cancelled' ? 'text-red-800' : 'text-gray-700' }}">
                @if($workshop->status->value === 'cancelled')
                    ⚠ This workshop has been cancelled. Registration is closed. Existing registrations are preserved below.
                @else
                    ✓ This workshop has been completed.
                @endif
            </p>
        </div>
    @elseif($workshop->starts_at->isPast())
        <div class="mb-4 rounded-md p-4 bg-yellow-50 border border-yellow-200">
            <p class="text-sm font-medium text-yellow-800">
                ⚠ This workshop has already started. New registrations are not accepted.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left column: Details + Registrations --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Workshop Details --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Workshop Details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Instructor</dt>
                        <dd class="text-gray-900">{{ $workshop->instructor }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Location</dt>
                        <dd class="text-gray-900">{{ $workshop->location }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Starts</dt>
                        <dd class="text-gray-900">{{ $workshop->starts_at->format('D, d M Y \a\t H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Ends</dt>
                        <dd class="text-gray-900">{{ $workshop->ends_at ? $workshop->ends_at->format('D, d M Y \a\t H:i') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Status</dt>
                        <dd>@include('components.status-badge', ['status' => $workshop->status])</dd>
                    </div>
                    @if($workshop->description)
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-gray-500">Description</dt>
                        <dd class="text-gray-900 mt-1">{{ $workshop->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Active Registrations --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">
                    Active Registrations
                    <span class="ml-2 inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-700">
                        {{ $activeRegistrations->count() }}
                    </span>
                </h2>

                @if($activeRegistrations->isEmpty())
                    <p class="text-sm text-gray-400 py-4 text-center">No active registrations yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Registered by</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">When</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($activeRegistrations as $reg)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 font-medium text-gray-900">{{ $reg->attendee_name }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $reg->attendee_email }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $reg->registeredBy->name ?? '—' }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $reg->registered_at->diffForHumans() }}</td>
                                        <td class="px-3 py-2">
                                            @can('cancel', $reg)
                                            <details class="relative">
                                                <summary class="cursor-pointer text-red-600 hover:text-red-800 font-medium text-xs">Cancel</summary>
                                                <div class="absolute right-0 mt-1 z-10 w-64 bg-white rounded-md shadow-lg border border-gray-200 p-3">
                                                    <form method="POST" action="{{ route('registrations.cancel', $reg) }}">
                                                        @csrf
                                                        <p class="text-xs text-gray-600 mb-2">Cancel registration for <strong>{{ $reg->attendee_name }}</strong>?</p>
                                                        <input type="text" name="cancel_reason" placeholder="Reason (optional)"
                                                               class="block w-full rounded border border-gray-300 px-2 py-1 text-xs mb-2 focus:border-indigo-400 focus:outline-none">
                                                        <button type="submit"
                                                                class="w-full rounded bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500 transition">
                                                            Confirm Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Full History --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Full Registration History</h2>
                @if($allRegistrations->isEmpty())
                    <p class="text-sm text-gray-400 py-4 text-center">No registration history.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name / Email</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Registered by / When</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cancelled by / When / Reason</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($allRegistrations as $reg)
                                    <tr class="hover:bg-gray-50 {{ $reg->isCancelled() ? 'opacity-60' : '' }}">
                                        <td class="px-3 py-2">
                                            <div class="font-medium text-gray-900">{{ $reg->attendee_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $reg->attendee_email }}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            @include('components.status-badge', ['status' => $reg->status])
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500">
                                            {{ $reg->registeredBy->name ?? '—' }}<br>
                                            {{ $reg->registered_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500">
                                            @if($reg->isCancelled())
                                                {{ $reg->cancelledBy->name ?? '—' }}<br>
                                                {{ $reg->cancelled_at?->format('d M Y H:i') }}<br>
                                                @if($reg->cancel_reason)
                                                    <span class="italic">"{{ $reg->cancel_reason }}"</span>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right column: Seats + Registration Form --}}
        <div class="space-y-4">
            {{-- Seats card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Capacity</h2>
                @php
                    $taken = $workshop->active_registrations_count ?? 0;
                    $left = max(0, $workshop->capacity - $taken);
                @endphp
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $taken }}</div>
                        <div class="text-xs text-gray-500">taken</div>
                    </div>
                    <div class="text-gray-300 text-lg">/</div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $workshop->capacity }}</div>
                        <div class="text-xs text-gray-500">capacity</div>
                    </div>
                    <div class="ml-auto">
                        <span class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-semibold
                            {{ $left === 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $left }} seat{{ $left !== 1 ? 's' : '' }} left
                        </span>
                    </div>
                </div>

                {{-- Capacity bar --}}
                <div class="mt-3 h-2 rounded-full bg-gray-100 overflow-hidden">
                    @php $pct = $workshop->capacity > 0 ? min(100, round($taken / $workshop->capacity * 100)) : 0; @endphp
                    <div class="h-full rounded-full {{ $left === 0 ? 'bg-red-500' : 'bg-green-500' }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>

            {{-- Registration form --}}
            @if($workshop->isOpenForRegistration())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">Register an Attendee</h2>
                    <form method="POST" action="{{ route('registrations.store', $workshop) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Full name</label>
                            <input type="text" name="attendee_name" value="{{ old('attendee_name') }}" required
                                   class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400
                                          @error('attendee_name') border-red-400 bg-red-50 @enderror">
                            @error('attendee_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Email address</label>
                            <input type="email" name="attendee_email" value="{{ old('attendee_email') }}" required
                                   class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400
                                          @error('attendee_email') border-red-400 bg-red-50 @enderror">
                            @error('attendee_email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition
                                       {{ $left === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $left === 0 ? 'disabled' : '' }}>
                            Register Attendee
                        </button>
                        @if($left === 0)
                            <p class="text-xs text-red-600 text-center">This workshop is full — 0 seats left.</p>
                        @endif
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
