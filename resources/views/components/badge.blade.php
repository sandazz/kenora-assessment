@props(['type' => 'default'])

@php
$classes = match($type) {
    'scheduled', 'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
    'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
    default => 'bg-indigo-100 text-indigo-800 border-indigo-200',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $classes]) }}>
    {{ $slot }}
</span>
