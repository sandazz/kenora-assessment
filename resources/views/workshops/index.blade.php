<x-app-layout>
    <x-slot name="title">Workshops</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900">Workshops</h1>
            @can('create', App\Models\Workshop::class)
                <a href="{{ route('workshops.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                    + New Workshop
                </a>
            @endcan
        </div>
    </x-slot>

    {{-- Filter bar --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('workshops.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Code, title, instructor…"
                           class="block w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Location</label>
                    <select name="location" class="block w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-400 focus:outline-none">
                        <option value="">All locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" class="block w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-400 focus:outline-none">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">From date</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                           class="block w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">To date</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                           class="block w-full rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-400 focus:outline-none">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="available_only" value="1" {{ request('available_only') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        Seats available only
                    </label>
                </div>
                <div class="flex items-end gap-2 col-span-1 sm:col-span-2 lg:col-span-2">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-indigo-500 transition">
                        Search
                    </button>
                    <a href="{{ route('workshops.index') }}" class="rounded-md border border-gray-300 px-4 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition">
                        Clear
                    </a>
                    {{-- Quick date buttons --}}
                    <a href="{{ route('workshops.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}"
                       class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition">Today</a>
                    <a href="{{ route('workshops.index', ['from' => now()->startOfWeek()->toDateString(), 'to' => now()->endOfWeek()->toDateString()]) }}"
                       class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition hidden sm:inline-flex">This week</a>
                    <a href="{{ route('workshops.index', ['from' => now()->toDateString(), 'to' => now()->addDays(7)->toDateString()]) }}"
                       class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition hidden md:inline-flex">Next 7 days</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Results table --}}
    @if($workshops->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <div class="text-4xl mb-3">📋</div>
            <p class="text-lg font-medium text-gray-500">No workshops found</p>
            <p class="text-sm">Try adjusting your filters or check back later.</p>
            @can('create', App\Models\Workshop::class)
                <a href="{{ route('workshops.create') }}" class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition">
                    Create the first workshop
                </a>
            @endcan
        </div>
    @else
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code / Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($workshops as $workshop)
                            @php
                                $taken = $workshop->active_registrations_count ?? 0;
                                $left = max(0, $workshop->capacity - $taken);
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="text-xs font-mono text-gray-500">{{ $workshop->code }}</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $workshop->title }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $workshop->instructor }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $workshop->location }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $workshop->starts_at->format('D, d M Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ $workshop->starts_at->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-600">{{ $taken }}/{{ $workshop->capacity }}</span>
                                    <span class="ml-1 inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $left === 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $left }} left
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @include('components.status-badge', ['status' => $workshop->status])
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('workshops.show', $workshop) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $workshops->links() }}
        </div>
    @endif
</x-app-layout>
