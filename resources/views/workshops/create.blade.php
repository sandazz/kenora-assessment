<x-app-layout>
    <x-slot name="title">Create Workshop</x-slot>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">Create Workshop</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6">
            <form action="{{ route('workshops.store') }}" method="POST">
                @include('workshops._form')

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('workshops.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Save Workshop</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
