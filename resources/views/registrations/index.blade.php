<x-app-layout>
    <x-slot name="title">Global Registration History</x-slot>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">Global Registration History</h1>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('registrations.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="workshop_id" class="block text-xs font-medium text-gray-700">Workshop</label>
                    <select name="workshop_id" id="workshop_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm py-1.5 border">
                        <option value="">All Workshops</option>
                        @foreach($workshops as $w)
                            <option value="{{ $w->id }}" {{ request('workshop_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->code }} — {{ $w->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm py-1.5 border">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-800">Filter</button>
                    <a href="{{ route('registrations.index') }}" class="px-3 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workshop</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cancelled Info</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse($registrations as $reg)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $reg->attendee_name }}</div>
                                <div class="text-xs text-gray-500">{{ $reg->attendee_email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('workshops.show', $reg->workshop) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    <span class="font-mono text-xs">{{ $reg->workshop->code }}</span> — {{ $reg->workshop->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :type="$reg->status->value">{{ ucfirst($reg->status->value) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                {{ $reg->registered_at->format('M j, Y g:i A') }}
                                <span class="block text-gray-400">by {{ $reg->registeredBy->name ?? 'System' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                @if($reg->status->value === 'cancelled')
                                    <span>{{ $reg->cancelled_at ? $reg->cancelled_at->format('M j, Y g:i A') : 'N/A' }}</span>
                                    <span class="block text-gray-400">by {{ $reg->cancelledBy->name ?? 'System' }}</span>
                                    @if($reg->cancel_reason)
                                        <span class="block italic text-red-600 font-medium">"{{ $reg->cancel_reason }}"</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No registrations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $registrations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
