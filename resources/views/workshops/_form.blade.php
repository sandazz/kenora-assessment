@csrf
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Workshop Code *</label>
            <input type="text" name="code" id="code" value="{{ old('code', $workshop->code ?? '') }}" required placeholder="e.g. POT-101" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $workshop->title ?? '') }}" required placeholder="e.g. Pottery Basics" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="instructor" class="block text-sm font-medium text-gray-700">Instructor *</label>
            <input type="text" name="instructor" id="instructor" value="{{ old('instructor', $workshop->instructor ?? '') }}" required placeholder="e.g. Jane Doe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            @error('instructor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">Location *</label>
            <select name="location" id="location" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
                @foreach(['Downtown', 'North Campus', 'Westside'] as $loc)
                    <option value="{{ $loc }}" {{ old('location', $workshop->location ?? '') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
            @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border" placeholder="Workshop details and prerequisites...">{{ old('description', $workshop->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="starts_at" class="block text-sm font-medium text-gray-700">Starts At *</label>
            <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', isset($workshop->starts_at) ? $workshop->starts_at->format('Y-m-d\TH:i') : '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="ends_at" class="block text-sm font-medium text-gray-700">Ends At (Optional)</label>
            <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', isset($workshop->ends_at) ? $workshop->ends_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            <p class="mt-1 text-xs text-gray-500">Defaults to starts + 2 hours if omitted.</p>
            @error('ends_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity *</label>
            <input type="number" name="capacity" id="capacity" min="1" value="{{ old('capacity', $workshop->capacity ?? 10) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            @error('capacity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    @if(isset($workshop))
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
        <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
            @foreach(\App\Enums\WorkshopStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ old('status', $workshop->status->value) === $status->value ? 'selected' : '' }}>
                    {{ ucfirst($status->value) }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    @endif
</div>
