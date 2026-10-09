<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Workshop Registration System — Community Training Centre">
    <title>{{ $title ?? 'Workshop Registration' }} — Kenora Training Centre</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        {{-- Navigation --}}
        <nav class="bg-indigo-700 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <span class="text-white font-bold text-lg tracking-tight">🏛️ Kenora Training</span>
                        <div class="hidden md:flex items-center gap-1">
                            @auth
                                @if(auth()->user()->role->canViewWorkshops())
                                    <a href="{{ route('workshops.index') }}"
                                       class="px-3 py-2 rounded-md text-sm font-medium text-indigo-100 hover:bg-indigo-600 hover:text-white transition
                                              {{ request()->routeIs('workshops.*') ? 'bg-indigo-800 text-white' : '' }}">
                                        Workshops
                                    </a>
                                    <a href="{{ route('registrations.index') }}"
                                       class="px-3 py-2 rounded-md text-sm font-medium text-indigo-100 hover:bg-indigo-600 hover:text-white transition
                                              {{ request()->routeIs('registrations.index') ? 'bg-indigo-800 text-white' : '' }}">
                                        Registrations
                                    </a>
                                @endif

                                @if(auth()->user()->role->canManageUsers())
                                    <a href="{{ route('admin.users.index') }}"
                                       class="px-3 py-2 rounded-md text-sm font-medium text-indigo-100 hover:bg-indigo-600 hover:text-white transition
                                              {{ request()->routeIs('admin.users.*') ? 'bg-indigo-800 text-white' : '' }}">
                                        Manage Users
                                    </a>
                                @endif

                                @if(auth()->user()->role->canManageUsers() || auth()->user()->role->canManageWorkshops())
                                    <a href="{{ route('admin.audit') }}"
                                       class="px-3 py-2 rounded-md text-sm font-medium text-indigo-100 hover:bg-indigo-600 hover:text-white transition
                                              {{ request()->routeIs('admin.audit') ? 'bg-indigo-800 text-white' : '' }}">
                                        Audit Log
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    @auth
                    <div class="flex items-center gap-3">
                        <span class="text-indigo-200 text-sm hidden sm:block">
                            {{ auth()->user()->name }}
                            <span class="ml-1 inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium bg-indigo-600 text-indigo-100">
                                {{ auth()->user()->role->label() }}
                            </span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-md text-sm font-medium text-indigo-100 border border-indigo-500 hover:bg-indigo-600 hover:text-white transition">
                                Sign out
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Page header --}}
        @isset($header)
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        {{-- Flash messages --}}
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mt-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <span class="text-green-600 mr-2">✓</span>
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @if(session('warning'))
                <div class="mt-4 rounded-md bg-yellow-50 p-4 border border-yellow-200">
                    <div class="flex">
                        <span class="text-yellow-600 mr-2">⚠</span>
                        <p class="text-sm text-yellow-800">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="mt-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <span class="text-red-600 mr-2">✕</span>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        {{-- Main content --}}
        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <footer class="mt-12 border-t border-gray-200 bg-white py-4">
            <p class="text-center text-xs text-gray-400">Kenora Community Training Centre — Workshop Registration System</p>
        </footer>
    </div>
</body>
</html>
